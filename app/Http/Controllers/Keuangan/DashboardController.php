<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ringkasan Angka Utama
        $pendingPayments = Pembayaran::where('status', 'pending')->count();
        $paidCount       = Pembayaran::where('status', 'paid')->count();
        $rejectedCount   = Pembayaran::where('status', 'rejected')->count();
        $totalRevenue    = Pembayaran::where('status', 'paid')->sum('jumlah_bayar');

        // 2. Rekap Pendapatan 12 Bulan (Tahun Berjalan) untuk $chartData
        $revenueBulanan = Pembayaran::where('status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[$i] = $revenueBulanan[$i] ?? 0;
        }

        $maxAmount = max(array_values($chartData)) ?: 1;

        // 3. Kalkulasi Persentase Donut Chart
        $totalTransaksi = $paidCount + $pendingPayments + $rejectedCount;
        $pctPaid        = $totalTransaksi > 0 ? round(($paidCount / $totalTransaksi) * 100) : 0;
        $pctPending     = $totalTransaksi > 0 ? round(($pendingPayments / $totalTransaksi) * 100) : 0;
        $pctRejected    = $totalTransaksi > 0 ? round(($rejectedCount / $totalTransaksi) * 100) : 0;

        // 4. Daftar Transaksi Terbaru
        $recentPayments = Pembayaran::with('user')
            ->latest()
            ->take(5)
            ->get();

        // Pastikan 'chartData' dan variabel lainnya dikirim ke view
        return view('keuangan.dashboard', compact(
            'pendingPayments',
            'paidCount',
            'rejectedCount',
            'totalRevenue',
            'chartData',
            'maxAmount',
            'totalTransaksi',
            'pctPaid',
            'pctPending',
            'pctRejected',
            'recentPayments'
        ));
    }
}