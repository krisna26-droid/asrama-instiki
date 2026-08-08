<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Ambil data reservasi terbaru milik user (termasuk relasi kamar)
        $reservasi = Reservasi::with('kamar')
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        // 2. Ambil transaksi pembayaran paling akhir untuk kartu indikator status
        $pembayaranTerakhir = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->first();

        // 3. Rekap total pembayaran per bulan di tahun berjalan (2026) untuk grafik
        $pembayaranBulanan = Pembayaran::where('user_id', $user->id)
            ->where('status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // 4. Susun array nominal 12 bulan (Jan - Des), default 0 jika belum ada transaksi
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = $pembayaranBulanan[$i] ?? 0;
        }

        // Cari nominal tertinggi untuk skala persentase tinggi batang grafik (bar height)
        $maxAmount = max(array_values($chartData)) ?: 1;

        return view('penghuni.dashboard', compact(
            'reservasi', 
            'pembayaranTerakhir', 
            'chartData', 
            'maxAmount'
        ));
    }
}