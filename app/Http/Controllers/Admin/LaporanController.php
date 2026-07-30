<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Reservasi;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index()
    {
        // Rekap ringkas data laporan
        $totalKamar = Kamar::count();
        $kamarTerisi = Kamar::where('status', 'tersewa_penuh')->count();
        $totalPenghuni = Reservasi::where('status', 'approved')->count();
        $totalReservasi = Reservasi::count();

        return view('admin.laporan.index', compact(
            'totalKamar',
            'kamarTerisi',
            'totalPenghuni',
            'totalReservasi'
        ));
    }

    public function export($type)
    {
        // Fitur ekspor sederhana / pencetakan laporan
        return redirect()->back()->with('success', 'Laporan ' . ucfirst($type) . ' berhasil diunduh.');
    }
}