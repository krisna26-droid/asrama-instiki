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

        if ($request->has('status') && $request->status != 'semua' && $request->status != '') {
            $query->where('status', $request->status);
        }

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
        
        // Ambil kamar yang belum penuh dan tidak dalam perbaikan
        $kamars = Kamar::where('status', '!=', 'perbaikan')
            ->whereColumn('terisi', '<', 'kapasitas')
            ->get();

        return view('admin.reservasi.index', compact('reservasis', 'kamars'));
    }

    public function approve(Request $request, Reservasi $reservasi)
    {
        if ($request->has('kamar_id') && $request->kamar_id != '') {
            $reservasi->kamar_id = $request->kamar_id;
        }

        $reservasi->status = 'approved';
        $reservasi->save();

        if ($reservasi->kamar) {
            $kamar = $reservasi->kamar;
            $kamar->terisi = min($kamar->kapasitas, $kamar->terisi + 1);
            
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