<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    public function index()
    {
        return view('keuangan.laporan.index');
    }

    public function export($type)
    {
        // Fungsi simulasi unduh/ekspor laporan keuangan
        return redirect()->back()->with('success', 'Laporan ' . str_replace('-', ' ', ucfirst($type)) . ' berhasil diunduh.');
    }
}