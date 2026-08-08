<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with(['user', 'reservasi.kamar']);

        // Filter berdasarkan status (all, pending, paid, rejected)
        if ($request->has('status') && $request->status != 'semua' && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Pencarian berdasarkan nama, NIM, atau kode pembayaran
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim_nik', 'like', "%{$search}%");
                  });
            });
        }

        $pembayarans = $query->latest()->get();

        return view('keuangan.pembayaran.index', compact('pembayarans'));
    }

    public function verify(Pembayaran $pembayaran)
    {
        $pembayaran->status = 'paid';
        $pembayaran->save();

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'catatan_keuangan' => 'required|string',
        ]);

        $pembayaran->status = 'rejected';
        $pembayaran->catatan_keuangan = $request->catatan_keuangan;
        $pembayaran->save();

        return redirect()->back()->with('success', 'Pembayaran telah ditolak.');
    }
    public function riwayat(Request $request)
    {
        // Mengambil transaksi yang statusnya sudah diproses (paid atau rejected)
        $query = Pembayaran::with(['user', 'reservasi.kamar'])
            ->whereIn('status', ['paid', 'rejected']);

        // Pencarian berdasarkan nama, NIM, atau kode pembayaran
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim_nik', 'like', "%{$search}%");
                });
            });
        }

        $riwayats = $query->latest()->get();

        return view('keuangan.riwayat.index', compact('riwayats'));
    }
}