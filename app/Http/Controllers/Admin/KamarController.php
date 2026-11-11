<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\CabangAsrama;
use App\Models\PerpanjanganPenempatan;
use App\Models\PenempatanPenghuni;
use App\Models\Reservasi;
use App\Models\User;
use App\Services\RoomOccupancyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::query()->with('cabangAsrama')->withCount(['penempatans as penghuni_aktif_count' => fn ($placement) => $placement
            ->where('status', 'active')->whereNotNull('check_in_at')->whereNull('check_out_at')
            ->whereDate('tanggal_mulai', '<=', now()->toDateString())->whereDate('tanggal_akhir', '>', now()->toDateString())]);
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(fn ($builder) => $builder->where('nomor_kamar', 'like', "%{$term}%")
                ->orWhere('blok', 'like', "%{$term}%"));
        }
        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->string('kategori')->toString());
        }
        if ($request->filled('cabang_asrama_id')) {
            $query->where('cabang_asrama_id', $request->integer('cabang_asrama_id'));
        }
        if ($request->filled('aktif') && in_array($request->input('aktif'), ['0', '1'], true)) {
            $query->where('is_active', $request->boolean('aktif'));
        }
        if ($request->filled('kapasitas_min')) {
            $query->where('kapasitas', '>=', $request->integer('kapasitas_min'));
        }
        if ($request->filled('kapasitas_max')) {
            $query->where('kapasitas', '<=', $request->integer('kapasitas_max'));
        }

        $kamars = $query->orderBy('nomor_kamar')->paginate(12)->withQueryString();
        $cabangs = CabangAsrama::query()->orderBy('nama')->get();
        return view('admin.kamar.index', compact('kamars', 'cabangs'));
    }

    public function store(Request $request)
    {
        $data = $this->validatedRoom($request);

        $uploadedPhoto = $this->storeUploadedPhoto($request);
        if ($uploadedPhoto !== null) {
            $data['foto'] = $uploadedPhoto;
        }

        try {
            DB::transaction(function () use ($data): void {
                // Room allocation also locks rooms before branches; preserve that order.
                Kamar::query()->orderBy('id')->lockForUpdate()->get(['id']);
                $branch = CabangAsrama::query()->whereKey($data['cabang_asrama_id'])->lockForUpdate()->first();
                if (!$branch || !$branch->is_active) {
                    throw ValidationException::withMessages(['cabang_asrama_id' => 'Pilih cabang asrama yang aktif.']);
                }

                $room = new Kamar();
                $this->assignRoomFields($room, $data);
                $room->is_active = true;
                $room->foto = filled($data['foto'] ?? null) ? $data['foto'] : null;
                $room->save();
            }, 3);
        } catch (Throwable $exception) {
            $this->removeUploadedPhotoAfterFailure($uploadedPhoto);
            throw $exception;
        }

        return back()->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function update(Request $request, Kamar $kamar, RoomOccupancyService $occupancy)
    {
        $data = $this->validatedRoom($request, $kamar);
        $uploadedPhoto = $this->storeUploadedPhoto($request);
        if ($uploadedPhoto !== null) {
            $data['foto'] = $uploadedPhoto;
        }

        try {
            DB::transaction(function () use ($kamar, $data, $occupancy) {
                $snapshot = PenempatanPenghuni::query()->where('kamar_id', $kamar->id)
                    ->whereIn('status', ['scheduled', 'active', 'no_show'])
                    ->get(['id', 'reservasi_id', 'user_id']);
                $placements = $this->lockRoomContext($snapshot, [$kamar->cabang_asrama_id, $data['cabang_asrama_id']]);
                $branch = CabangAsrama::query()->whereKey($data['cabang_asrama_id'])->first();
                if (!$branch || !$branch->is_active) {
                    throw ValidationException::withMessages(['cabang_asrama_id' => 'Pilih cabang asrama yang aktif.']);
                }

                if ($occupancy->peakConcurrentOccupancy($placements) > $data['kapasitas']) {
                    throw ValidationException::withMessages(['kapasitas' => 'Kapasitas baru di bawah puncak penempatan aktif/terjadwal saat ini.']);
                }

                $lockedRoom = Kamar::query()->whereKey($kamar->id)->firstOrFail();
                $currentBranchId = $lockedRoom->cabang_asrama_id === null ? null : (int) $lockedRoom->cabang_asrama_id;
                $requestedBranchId = (int) $data['cabang_asrama_id'];
                if ($currentBranchId !== null && $currentBranchId !== $requestedBranchId
                    && PenempatanPenghuni::query()->where('kamar_id', $lockedRoom->id)->exists()) {
                    throw ValidationException::withMessages(['cabang_asrama_id' => 'Cabang kamar yang sudah memiliki histori placement tidak dapat dipindahkan. Buat kamar baru untuk lokasi berbeda.']);
                }
                if ($placements->contains(fn ($placement) => in_array($placement->status, ['scheduled', 'active', 'no_show'], true)
                    && $placement->user?->jenis_kelamin !== $data['kategori'])) {
                    throw ValidationException::withMessages(['kategori' => 'Kategori kamar tidak dapat diubah selama ada penempatan yang tidak sesuai.']);
                }
                if ($data['status'] === 'perbaikan' && $placements->contains(fn ($placement) => in_array($placement->status, ['scheduled', 'active', 'no_show'], true))) {
                    throw ValidationException::withMessages(['status' => 'Kamar dengan penempatan aktif, terjadwal, atau no-show belum dapat dialihkan ke status perbaikan. Selesaikan penempatan melalui proses administrasi terlebih dahulu.']);
                }

                $this->assignRoomFields($lockedRoom, $data);
                if (filled($data['foto'] ?? null)) {
                    $lockedRoom->foto = $data['foto'];
                }
                $lockedRoom->save();
            }, 3);
        } catch (Throwable $exception) {
            $this->removeUploadedPhotoAfterFailure($uploadedPhoto);
            throw $exception;
        }

        return back()->with('success', 'Data kamar diperbarui; histori penempatan tetap dipertahankan.');
    }

    /** The existing DELETE endpoint is now an archive action; room rows are never deleted. */
    public function destroy(Kamar $kamar)
    {
        DB::transaction(function () use ($kamar) {
            $snapshot = PenempatanPenghuni::query()->where('kamar_id', $kamar->id)->get(['id', 'reservasi_id', 'user_id']);
            $placements = $this->lockRoomContext($snapshot, [$kamar->cabang_asrama_id]);
            $lockedRoom = Kamar::query()->whereKey($kamar->id)->firstOrFail();
            if ($placements->contains(fn ($placement) => in_array($placement->status, ['scheduled', 'active', 'no_show'], true))) {
                throw ValidationException::withMessages(['kamar' => 'Kamar dengan penempatan aktif, terjadwal, atau no-show tidak dapat diarsipkan.']);
            }
            $lockedRoom->is_active = false;
            $lockedRoom->save();
        }, 3);

        return back()->with('success', 'Kamar dinonaktifkan sebagai arsip; histori dan relasi tetap utuh.');
    }

    public function toggleActive(Kamar $kamar)
    {
        DB::transaction(function () use ($kamar) {
            $snapshot = PenempatanPenghuni::query()->where('kamar_id', $kamar->id)->get(['id', 'reservasi_id', 'user_id']);
            $placements = $this->lockRoomContext($snapshot, [$kamar->cabang_asrama_id]);
            $lockedRoom = Kamar::query()->whereKey($kamar->id)->firstOrFail();
            if ($lockedRoom->is_active && $placements->contains(fn ($placement) => in_array($placement->status, ['scheduled', 'active', 'no_show'], true))) {
                throw ValidationException::withMessages(['kamar' => 'Kamar dengan penempatan aktif, terjadwal, atau no-show tidak dapat dinonaktifkan.']);
            }
            $lockedRoom->is_active = !$lockedRoom->is_active;
            $lockedRoom->save();
        }, 3);
        return back()->with('success', 'Status aktif kamar diperbarui tanpa mengubah placement historis.');
    }

    private function validatedRoom(Request $request, ?Kamar $kamar = null): array
    {
        return $request->validate([
            'nomor_kamar' => ['required', 'string', 'max:255', Rule::unique('kamars', 'nomor_kamar')->ignore($kamar?->id)],
            'cabang_asrama_id' => ['required', 'integer', 'exists:cabang_asramas,id'],
            'blok' => ['required', 'string', 'max:30'],
            'lantai' => ['required', 'integer', 'min:0'],
            'kategori' => ['required', 'in:putra,putri'],
            'kapasitas' => ['required', 'integer', 'min:1'],
            'harga_bulanan' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:tersedia,tersewa_penuh,perbaikan'],
            'fasilitas' => ['nullable', 'string', 'max:5000'],
            'foto' => ['nullable', 'url', 'max:2048'],
            'foto_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);
    }

    private function storeUploadedPhoto(Request $request): ?string
    {
        $file = $request->file('foto_file');
        if ($file === null) {
            return null;
        }

        if (! $file->isValid()) {
            throw ValidationException::withMessages(['foto_file' => 'Unggahan foto tidak lengkap atau gagal diterima server. Silakan pilih ulang file.']);
        }

        $disk = Storage::disk('public');
        $path = $file->storePublicly('kamars', 'public');
        if ($path === false || ! $disk->exists($path)) {
            throw ValidationException::withMessages(['foto_file' => 'Foto kamar gagal disimpan. Silakan coba kembali.']);
        }

        return $path;
    }

    private function removeUploadedPhotoAfterFailure(?string $path): void
    {
        if ($path !== null) {
            Storage::disk('public')->delete($path);
        }
    }

    private function assignRoomFields(Kamar $room, array $data): void
    {
        foreach (['nomor_kamar', 'cabang_asrama_id', 'blok', 'lantai', 'kategori', 'kapasitas', 'harga_bulanan', 'status', 'fasilitas'] as $field) {
            $room->{$field} = $data[$field] ?? null;
        }
    }

    /** Lock reservations -> users -> rooms -> placements in ascending IDs. */
    private function lockRoomContext($placements, array $branchIds = [])
    {
        $reservationIds = $placements->pluck('reservasi_id')->filter()->unique()->sort()->values();
        $userIds = $placements->pluck('user_id')->filter()->unique()->sort()->values();
        if ($reservationIds->isNotEmpty()) {
            Reservasi::query()->whereIn('id', $reservationIds)->orderBy('id')->lockForUpdate()->get();
        }
        if ($userIds->isNotEmpty()) {
            User::query()->whereIn('id', $userIds)->orderBy('id')->lockForUpdate()->get();
        }
        $lockedRooms = Kamar::query()->orderBy('id')->lockForUpdate()->get(['id', 'cabang_asrama_id']);

        $placementIds = $placements->pluck('id')->filter()->unique()->sort()->values();
        $lockedPlacements = $placementIds->isEmpty()
            ? collect()
            : PenempatanPenghuni::query()->with('user')->whereIn('id', $placementIds)->orderBy('id')->lockForUpdate()->get();
        if ($placementIds->isNotEmpty()) {
            PerpanjanganPenempatan::query()->whereIn('penempatan_id', $placementIds)->orderBy('id')->lockForUpdate()->get();
        }
        $this->lockBranchRows(array_merge(
            $branchIds,
            $placements->pluck('cabang_asrama_id')->all(),
            $lockedPlacements->pluck('cabang_asrama_id')->all(),
            $lockedRooms->pluck('cabang_asrama_id')->all(),
        ));

        return $lockedPlacements;
    }

    private function lockBranchRows(array $branchIds): void
    {
        $ids = collect($branchIds)->filter()->map(fn ($id) => (int) $id)->unique()->sort()->values();
        if ($ids->isNotEmpty()) {
            CabangAsrama::query()->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
        }
    }
}
