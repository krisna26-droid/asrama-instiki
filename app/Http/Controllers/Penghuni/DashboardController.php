<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Reservasi;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $reservasi = Reservasi::with(['kamar'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        $pembayaranTerakhir = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->first();

        return view('penghuni.dashboard', compact('reservasi', 'pembayaranTerakhir'));
    }
}