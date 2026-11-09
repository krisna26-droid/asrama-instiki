<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Services\ReservationLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservasi::query()->with(['user', 'kamar', 'penempatans.kamar']);

        if ($request->filled('status') && $request->string('status')->toString() !== 'semua') {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(function ($builder) use ($term) {
                $builder->where('kode_reservasi', 'like', "%{$term}%")
                    ->orWhereHas('user', function ($user) use ($term) {
                        $user->where('nama', 'like', "%{$term}%")
                            ->orWhere('nik', 'like', "%{$term}%");
                    });
            });
        }
        if ($request->filled('tanggal_dari')) {
            $query->whereDate('tanggal_pengajuan', '>=', $request->date('tanggal_dari'));
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('tanggal_pengajuan', '<=', $request->date('tanggal_sampai'));
        }

        $reservasis = $query->latest()->paginate(15)->withQueryString();
        return view('admin.reservasi.index', compact('reservasis'));
    }

    public function approve(Request $request, Reservasi $reservasi, ReservationLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'durasi_bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $lifecycle->approve($reservasi->id, $validated['tanggal_mulai'], isset($validated['durasi_bulan']) ? (int) $validated['durasi_bulan'] : null);
        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi disetujui, periode dan tagihan dihitung dari durasi, lalu kamar dialokasikan otomatis.');
    }

    public function reject(Request $request, Reservasi $reservasi)
    {
        $validated = $request->validate(['catatan_admin' => ['required', 'string', 'max:2000']]);

        DB::transaction(function () use ($reservasi, $validated) {
            $locked = Reservasi::query()->whereKey($reservasi->id)->lockForUpdate()->firstOrFail();
            if ($locked->status !== 'pending' || $locked->penempatans()->exists()) {
                throw ValidationException::withMessages(['reservasi' => 'Hanya reservasi pending yang dapat ditolak.']);
            }
            $locked->status = 'rejected';
            $locked->catatan_admin = $validated['catatan_admin'];
            $locked->save();
        }, 3);

        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi ditolak dengan alasan yang tersimpan.');
    }

    public function verifyLegacy(Request $request, Reservasi $reservasi, ReservationLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'tanggal_akhir' => ['required', 'date_format:Y-m-d', 'after:tanggal_mulai'],
        ]);
        $lifecycle->verifyLegacy($reservasi->id, $validated['tanggal_mulai'], $validated['tanggal_akhir']);
        return back()->with('success', 'Reservasi lama diverifikasi dan placement baru dibuat tanpa menghapus histori.');
    }
}
