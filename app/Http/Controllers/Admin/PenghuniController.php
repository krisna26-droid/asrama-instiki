<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use Illuminate\Http\Request;

class PenghuniController extends Controller
{
    public function index(Request $request)
    {
        // Mengambil data reservasi yang berstatus approved beserta relasi user dan kamar
        $query = Reservasi::with(['user', 'kamar'])
            ->where('status', 'approved');

        // Pencarian berdasarkan nama, NIM, nomor kamar, atau blok
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('nama', 'like', "%{$search}%")
                      ->orWhere('nim_nik', 'like', "%{$search}%");
                })
                ->orWhereHas('kamar', function ($k) use ($search) {
                    $k->where('nomor_kamar', 'like', "%{$search}%")
                      ->orWhere('blok', 'like', "%{$search}%");
                });
            });
        }

        $penghunis = $query->latest()->get();

        return view('admin.penghuni.index', compact('penghunis'));
    }

    public function show(Reservasi $reservasi)
    {
        $reservasi->load(['user', 'kamar']);
        return view('admin.penghuni.show', compact('reservasi'));
    }
}