<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReservasiController extends Controller
{
    public function index()
    {
        $reservasiAktif = Reservasi::with(['penempatans.kamar'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'approved', 'legacy_unverified', 'no_show'])
            ->latest()
            ->first();

        $reservasiRiwayat = Reservasi::query()
            ->with(['penempatans.kamar'])
            ->where('user_id', auth()->id())
            ->whereNotIn('status', ['pending', 'approved', 'legacy_unverified', 'no_show'])
            ->latest('tanggal_pengajuan')
            ->paginate(10, ['*'], 'riwayat_page')
            ->withQueryString();

        return view('penghuni.reservasi.index', compact('reservasiAktif', 'reservasiRiwayat'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'durasi_bulan' => ['required', 'integer', 'min:1', 'max:12'],
            'foto_ktp' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $user = $request->user();
        $file = $request->file('foto_ktp');
        if (! $file || ! $file->isValid()) {
            throw ValidationException::withMessages(['foto_ktp' => 'File KTP gagal diterima. Silakan pilih ulang file JPG, PNG, atau PDF.']);
        }

        $path = $file->store('dokumen-ktp', 'local');
        if (! is_string($path) || ! Storage::disk('local')->exists($path)) {
            throw ValidationException::withMessages(['foto_ktp' => 'File KTP gagal disimpan. Silakan coba kembali.']);
        }

        try {
            DB::transaction(function () use ($user, $path, $validated) {
                $lockedUser = $user->newQuery()->whereKey($user->id)->lockForUpdate()->firstOrFail();
                $hasOpenRequest = Reservasi::query()
                    ->where('user_id', $lockedUser->id)
                    ->whereIn('status', ['pending', 'approved', 'legacy_unverified', 'no_show'])
                    ->exists();

                if ($hasOpenRequest) {
                    throw ValidationException::withMessages([
                        'reservasi' => 'Anda masih memiliki reservasi yang menunggu atau disetujui.',
                    ]);
                }

                Reservasi::create([
                    'kode_reservasi' => 'RSV-' . now()->format('Y') . '-' . strtoupper(Str::random(8)),
                    'user_id' => $lockedUser->id,
                    'kamar_id' => null,
                    'tanggal_pengajuan' => now()->toDateString(),
                    'durasi_sewa' => (int) $validated['durasi_bulan'] . ' bulan',
                    'durasi_bulan' => (int) $validated['durasi_bulan'],
                    'status' => 'pending',
                    'foto_ktp_path' => $path,
                ]);
            }, 3);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return redirect()->route('penghuni.reservasi.index')->with('success', 'Pengajuan reservasi dan KTP berhasil dikirim. Admin Asrama akan menetapkan tanggal mulai dan sistem menghitung tanggal akhir serta tagihannya.');
    }
}
