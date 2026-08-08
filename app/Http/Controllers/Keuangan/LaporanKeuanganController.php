<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanKeuanganController extends Controller
{
    public function index()
    {
        // 1. Rekap Pendapatan Lunas Per Bulan (Tahun Berjalan)
        $paidBulanan = Pembayaran::where('status', 'paid')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        // 2. Rekap Nominal Menunggu Verifikasi Per Bulan (Tahun Berjalan)
        $pendingBulanan = Pembayaran::where('status', 'pending')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('MONTH(created_at) as bulan'),
                DB::raw('SUM(jumlah_bayar) as total')
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartPaid = [];
        $chartPending = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartPaid[$i] = $paidBulanan[$i] ?? 0;
            $chartPending[$i] = $pendingBulanan[$i] ?? 0;
        }

        // Cari nominal tertinggi untuk skala persentase tinggi grafik
        $allAmounts = array_merge(array_values($chartPaid), array_values($chartPending));
        $maxAmount = max($allAmounts) ?: 1;

        return view('keuangan.laporan.index', compact('chartPaid', 'chartPending', 'maxAmount'));
    }

    public function export($type)
    {
        $filename = 'Laporan_' . ucfirst($type) . '_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan UTF-8 BOM agar terbaca sempurna berkolom di Excel & WPS
            fputs($file, "\xEF\xBB\xBF");

            // Fungsi pembantu penulisan baris dengan pemisah titik koma (;)
            $writeRow = function ($file, $array) {
                fputcsv($file, $array, ';');
            };

            switch ($type) {
                case 'pendapatan':
                    $writeRow($file, ['No', 'Kode Pembayaran', 'Nama Penghuni', 'NIM/NIK', 'Kamar', 'Periode', 'Jumlah (Rp)', 'Tanggal Bayar', 'Status']);
                    $data = Pembayaran::with(['user', 'reservasi.kamar'])
                        ->where('status', 'paid')
                        ->latest()
                        ->get();

                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->kode_pembayaran,
                            $row->user->nama ?? '-',
                            $row->user->nim_nik ?? '-',
                            $row->reservasi->kamar ? 'Kamar ' . $row->reservasi->kamar->nomor_kamar . ' (' . $row->reservasi->kamar->blok . ')' : '-',
                            \Carbon\Carbon::parse($row->created_at)->format('F Y'),
                            $row->jumlah_bayar,
                            \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d H:i'),
                            'LUNAS'
                        ]);
                    }
                    break;

                case 'log-pembayaran':
                    $writeRow($file, ['No', 'Kode Pembayaran', 'Nama Penghuni', 'NIM/NIK', 'Metode Bayar', 'Jumlah (Rp)', 'Status', 'Tanggal Transaksi']);
                    $data = Pembayaran::with('user')->latest()->get();

                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->kode_pembayaran,
                            $row->user->nama ?? '-',
                            $row->user->nim_nik ?? '-',
                            $row->metode_pembayaran,
                            $row->jumlah_bayar,
                            strtoupper($row->status),
                            \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i')
                        ]);
                    }
                    break;

                case 'tunggakan':
                    $writeRow($file, ['No', 'Kode Pembayaran', 'Nama Penghuni', 'NIM/NIK', 'No Telepon', 'Jumlah Tagihan (Rp)', 'Status', 'Tanggal Pengajuan']);
                    $data = Pembayaran::with('user')
                        ->where('status', 'pending')
                        ->latest()
                        ->get();

                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->kode_pembayaran,
                            $row->user->nama ?? '-',
                            $row->user->nim_nik ?? '-',
                            $row->user->no_telepon ?? '-',
                            $row->jumlah_bayar,
                            'MENUNGGU VERIFIKASI',
                            \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i')
                        ]);
                    }
                    break;

                case 'penolakan':
                    $writeRow($file, ['No', 'Kode Pembayaran', 'Nama Penghuni', 'NIM/NIK', 'Jumlah (Rp)', 'Alasan Penolakan', 'Tanggal Penolakan']);
                    $data = Pembayaran::with('user')
                        ->where('status', 'rejected')
                        ->latest()
                        ->get();

                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->kode_pembayaran,
                            $row->user->nama ?? '-',
                            $row->user->nim_nik ?? '-',
                            $row->jumlah_bayar,
                            $row->catatan_keuangan ?? 'Bukti tidak sesuai',
                            \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d H:i')
                        ]);
                    }
                    break;

                case 'metode-pembayaran':
                    $writeRow($file, ['No', 'Metode Pembayaran', 'Total Transaksi Lunas', 'Total Nominal (Rp)']);
                    $data = Pembayaran::where('status', 'paid')
                        ->select('metode_pembayaran', DB::raw('COUNT(*) as total_tx'), DB::raw('SUM(jumlah_bayar) as total_nominal'))
                        ->groupBy('metode_pembayaran')
                        ->get();

                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->metode_pembayaran,
                            $row->total_tx,
                            $row->total_nominal
                        ]);
                    }
                    break;

                case 'ringkasan-tahunan':
                    $writeRow($file, ['Bulan', 'Tahun', 'Jumlah Transaksi Lunas', 'Total Pendapatan (Rp)']);
                    for ($m = 1; $m <= 12; $m++) {
                        $txCount = Pembayaran::where('status', 'paid')
                            ->whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', $m)
                            ->count();

                        $totalNominal = Pembayaran::where('status', 'paid')
                            ->whereYear('created_at', date('Y'))
                            ->whereMonth('created_at', $m)
                            ->sum('jumlah_bayar');

                        $writeRow($file, [
                            date('F', mktime(0, 0, 0, $m, 1)),
                            date('Y'),
                            $txCount,
                            $totalNominal
                        ]);
                    }
                    break;

                default:
                    $writeRow($file, ['Error', 'Tipe laporan tidak valid']);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}