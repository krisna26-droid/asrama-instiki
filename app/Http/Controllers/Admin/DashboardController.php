<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Reservasi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalKamar      = Kamar::count();
        $kamarTersedia   = Kamar::where('status', 'tersedia')->count();
        $kamarPenuh      = Kamar::where('status', 'tersewa_penuh')->count();
        $kamarPerbaikan  = Kamar::where('status', 'perbaikan')->count();
        $pendingReservasi = Reservasi::where('status', 'pending')->count();
        $latestReservasi = Reservasi::with(['user', 'kamar'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalKamar',
            'kamarTersedia',
            'kamarPenuh',
            'kamarPerbaikan',
            'pendingReservasi',
            'latestReservasi'
        ));
    }
}
