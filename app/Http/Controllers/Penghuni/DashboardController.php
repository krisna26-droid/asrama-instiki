<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\PenempatanPenghuni;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Ambil data reservasi terbaru milik user (termasuk relasi kamar)
        $reservasi = Reservasi::query()
            ->where('user_id', $user->id)
            ->with(['cabangAsrama', 'kamar.cabangAsrama'])
            ->latest()
            ->first();
        $hasOpenReservation = Reservasi::query()->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved', 'legacy_unverified', 'no_show'])
            ->exists();

        $activePlacement = PenempatanPenghuni::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->whereDate('tanggal_mulai', '<=', now()->toDateString())
            ->whereDate('tanggal_akhir', '>', now()->toDateString())
            ->latest('id')->first();

        $assignedPlacements = PenempatanPenghuni::query()
            ->with(['kamar.cabangAsrama', 'cabangAsrama'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['scheduled', 'active'])
            ->whereHas('reservasi', fn ($query) => $query->where('status', 'approved'))
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('tanggal_mulai')
            ->get();

        // 2. Ambil transaksi pembayaran paling akhir untuk kartu indikator status
        $pembayaranTerakhir = Pembayaran::with('reservasi')->where('user_id', $user->id)
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
            'hasOpenReservation',
            'activePlacement',
            'assignedPlacements',
            'pembayaranTerakhir', 
            'chartData', 
            'maxAmount'
        ));
    }
}
