<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingPayments = Pembayaran::where('status', 'pending')->count();
        $paidCount       = Pembayaran::where('status', 'paid')->count();
        $rejectedCount   = Pembayaran::where('status', 'rejected')->count();
        $totalRevenue    = Pembayaran::where('status', 'paid')->sum('jumlah_bayar');

        $recentPayments  = Pembayaran::with(['user', 'reservasi.kamar'])
            ->latest()
            ->take(5)
            ->get();

        return view('keuangan.dashboard', compact(
            'pendingPayments',
            'paidCount',
            'rejectedCount',
            'totalRevenue',
            'recentPayments'
        ));
    }
}