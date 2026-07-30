<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Kamar;
use Illuminate\Http\Request;

class ReservasiController extends Controller
{
    public function index(Request $request)
    {
        $query = Reservasi::with(['user', 'kamar']);

        // Filter Status
        if ($request->has('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Pencarian
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_reservasi', 'like', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim_nik', 'like', "%{$search}%");
                  });
            });
        }

        $reservasis = $query->latest()->get();
        $kamars = Kamar::where('status', 'tersedia')->get();

        return view('admin.reservasi.index', compact('reservasis', 'kamars'));
    }

    public function approve(Request $request, Reservasi $reservasi)
    {
        // Jika admin memilihkan kamar saat approve
        if ($request->has('kamar_id')) {
            $reservasi->kamar_id = $request->kamar_id;
        }

        $reservasi->status = 'approved';
        $reservasi->save();

        // Update jumlah terisi pada kamar
        if ($reservasi->kamar) {
            $kamar = $reservasi->kamar;
            $kamar->terisi += 1;
            if ($kamar->terisi >= $kamar->kapasitas) {
                $kamar->status = 'tersewa_penuh';
            }
            $kamar->save();
        }

        return redirect()->back()->with('success', 'Reservasi berhasil disetujui.');
    }

    public function reject(Request $request, Reservasi $reservasi)
    {
        $reservasi->status = 'rejected';
        $reservasi->catatan_admin = $request->catatan_admin ?? 'Pengajuan tidak memenuhi syarat.';
        $reservasi->save();

        return redirect()->back()->with('success', 'Reservasi telah ditolak.');
    }
}