<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangAsrama;
use App\Models\Reservasi;
use App\Services\ReservationLifecycleService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservasi::query()->with(['user', 'kamar.cabangAsrama', 'cabangAsrama', 'penempatans.kamar.cabangAsrama', 'penempatans.cabangAsrama']);

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
        if ($request->filled('cabang_asrama_id')) {
            $query->where('cabang_asrama_id', $request->integer('cabang_asrama_id'));
        }

        $reservasis = $query->latest()->paginate(15)->withQueryString();
        $cabangs = CabangAsrama::query()->where('is_active', true)->orderBy('nama')->get();
        return view('admin.reservasi.index', compact('reservasis', 'cabangs'));
    }

    public function approve(Request $request, Reservasi $reservasi, ReservationLifecycleService $lifecycle)
    {
        $validated = $request->validate([
            'tanggal_mulai' => ['required', 'date_format:Y-m-d'],
            'cabang_asrama_id' => ['required', 'integer', 'exists:cabang_asramas,id'],
            'durasi_bulan' => ['nullable', 'integer', 'min:1', 'max:12'],
        ]);

        $lifecycle->approve($reservasi->id, $validated['tanggal_mulai'], (int) $validated['cabang_asrama_id'], isset($validated['durasi_bulan']) ? (int) $validated['durasi_bulan'] : null);
        return redirect()->route('admin.reservasi.index')->with('success', 'Reservasi disetujui. Sistem mengalokasikan kamar yang sesuai secara acak di cabang pilihan dan menghitung periode serta tagihan.');
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
            'cabang_asrama_id' => ['required', 'integer', 'exists:cabang_asramas,id'],
        ]);
        $lifecycle->verifyLegacy($reservasi->id, $validated['tanggal_mulai'], $validated['tanggal_akhir'], (int) $validated['cabang_asrama_id']);
        return back()->with('success', 'Reservasi lama diverifikasi dan kamar dialokasikan di cabang pilihan tanpa menghapus histori.');
    }
}
