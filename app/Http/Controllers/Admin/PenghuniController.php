<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenempatanPenghuni;
use App\Models\Kamar;
use App\Services\ReservationLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tanggal_dari' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_sampai' => ['nullable', 'date_format:Y-m-d'],
        ]);
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])
            && $filters['tanggal_sampai'] < $filters['tanggal_dari']) {
            throw ValidationException::withMessages(['tanggal_sampai' => 'Tanggal sampai harus sama atau setelah tanggal dari.']);
        }
        $status = $request->string('status', 'active')->toString();
        $query = PenempatanPenghuni::query()->with(['user', 'kamar', 'reservasi.pembayarans']);

        match ($status) {
            'scheduled' => $query->where('status', 'scheduled'),
            'history' => $query->whereIn('status', ['completed', 'cancelled', 'no_show']),
            default => $query->where('status', 'active')->whereNotNull('check_in_at')->whereNull('check_out_at')
                ->whereDate('tanggal_mulai', '<=', now()->toDateString())->whereDate('tanggal_akhir', '>', now()->toDateString()),
        };

        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(function ($builder) use ($term) {
                $builder->whereHas('user', fn ($user) => $user->where('nama', 'like', "%{$term}%")->orWhere('nik', 'like', "%{$term}%"))
                    ->orWhereHas('kamar', fn ($room) => $room->where('nomor_kamar', 'like', "%{$term}%"));
            });
        }
        if ($request->filled('jenis_kelamin')) {
            $query->whereHas('user', fn ($user) => $user->where('jenis_kelamin', $request->string('jenis_kelamin')->toString()));
        }
        if (!empty($filters['tanggal_dari'])) {
            $query->whereDate('tanggal_mulai', '>=', $filters['tanggal_dari']);
        }
        if (!empty($filters['tanggal_sampai'])) {
            $query->whereDate('tanggal_mulai', '<=', $filters['tanggal_sampai']);
        }

        $penempatans = $query->orderByDesc('tanggal_mulai')->paginate(15)->withQueryString();
        return view('admin.penghuni.index', compact('penempatans', 'status'));
    }

    public function show(PenempatanPenghuni $placement)
    {
        $placement->load(['user', 'kamar', 'reservasi.pembayarans', 'transferDari', 'transferKe.kamar']);
        $kamarsTujuan = Kamar::query()->where('is_active', true)->where('status', '!=', 'perbaikan')
            ->where('kategori', $placement->user->jenis_kelamin)->where('id', '!=', $placement->kamar_id)
            ->orderBy('nomor_kamar')->get(['id', 'nomor_kamar', 'blok', 'kapasitas']);
        return view('admin.penghuni.show', compact('placement', 'kamarsTujuan'));
    }

    public function checkIn(PenempatanPenghuni $placement, ReservationLifecycleService $lifecycle)
    {
        $lifecycle->checkIn($placement->id, (int) auth()->id());
        return back()->with('success', 'Check-in berhasil dicatat.');
    }

    public function rescheduleCheckIn(Request $request, PenempatanPenghuni $placement, ReservationLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'tanggal_mulai_saat_form' => ['required', 'date_format:Y-m-d'],
            'alasan' => ['required', 'string', 'max:2000'],
        ]);

        $lifecycle->rescheduleCheckIn(
            $placement->id,
            $validated['tanggal_mulai'],
            $validated['tanggal_mulai_saat_form'],
            (int) auth()->id(),
            $validated['alasan'],
        );

        return redirect()->route('admin.penghuni.show', $placement)
            ->with('success', 'Jadwal check-in dan periode penempatan berhasil diperbarui setelah pemeriksaan kapasitas.');
    }

    public function checkOut(Request $request, PenempatanPenghuni $placement, ReservationLifecycleService $lifecycle)
    {
        $validated = $request->validate(['alasan' => ['required', 'string', 'max:2000']]);
        $lifecycle->checkOut($placement->id, (int) auth()->id(), $validated['alasan']);
        return back()->with('success', 'Check-out berhasil dicatat dan placement mendatang dibatalkan.');
    }

    public function transfer(Request $request, PenempatanPenghuni $placement, ReservationLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'kamar_id' => ['required', 'integer', 'exists:kamars,id'],
            'alasan' => ['required', 'string', 'max:2000'],
        ]);
        $lifecycle->transferDirect($placement->id, (int) $validated['kamar_id'], (int) auth()->id(), $validated['alasan']);
        return redirect()->route('admin.penghuni.show', $placement)->with('success', 'Transfer langsung berhasil dicatat. Histori kamar asal tetap tersimpan.');
    }
}
