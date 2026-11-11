<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Reservasi;
use App\Models\Pembayaran;
use App\Models\PenempatanPenghuni;
use App\Services\RoomOccupancyService;
use App\Support\CsvCellSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request, RoomOccupancyService $occupancy)
    {
        $tanggalAcuan = $request->input('tanggal_acuan', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalAcuan) || !strtotime($tanggalAcuan)) {
            $tanggalAcuan = now()->toDateString();
        }
        $capacitySummary = $occupancy->capacitySummaryAt($tanggalAcuan);
        $branchCapacitySummaries = $occupancy->capacitySummaryByBranch($tanggalAcuan);
        $totalKamar = Kamar::where('is_active', true)->count();
        $kamarTerisi = PenempatanPenghuni::query()->where('status', 'active')->whereNotNull('check_in_at')->whereNull('check_out_at')->whereDate('tanggal_mulai', '<=', $tanggalAcuan)->whereDate('tanggal_akhir', '>', $tanggalAcuan)->distinct('kamar_id')->count('kamar_id');
        $totalPenghuni = $capacitySummary['penghuniAktif'];
        $totalReservasi = Reservasi::count();

        // 2. Data Grafik Bulanan (Okupansi Disetujui vs Reservasi Masuk) Tahun Berjalan
        $reservasiMasukBulanan = Reservasi::whereYear('tanggal_pengajuan', date('Y'))
            ->select(
                DB::raw('MONTH(tanggal_pengajuan) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $reservasiDisetujuiBulanan = Reservasi::where('status', 'approved')
            ->whereYear('tanggal_pengajuan', date('Y'))
            ->select(
                DB::raw('MONTH(tanggal_pengajuan) as bulan'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('bulan')
            ->pluck('total', 'bulan')
            ->toArray();

        $chartTotal = [];
        $chartApproved = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartTotal[$i]    = $reservasiMasukBulanan[$i] ?? 0;
            $chartApproved[$i] = $reservasiDisetujuiBulanan[$i] ?? 0;
        }

        $maxCount = max(array_merge(array_values($chartTotal), array_values($chartApproved))) ?: 1;

        return view('admin.laporan.index', compact(
            'totalKamar',
            'kamarTerisi',
            'totalPenghuni',
            'totalReservasi',
            'chartTotal',
            'chartApproved',
            'maxCount',
            'capacitySummary',
            'branchCapacitySummaries',
            'tanggalAcuan'
        ));
    }

    public function export($type)
    {
        $filename = 'Laporan_Asrama_' . ucfirst($type) . '_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($type) {
            $file = fopen('php://output', 'w');
            
            // Tambahkan UTF-8 BOM agar rapi berkolom di Excel & WPS Office
            fputs($file, "\xEF\xBB\xBF");

            $writeRow = function ($file, $array) {
                fputcsv($file, array_map([CsvCellSanitizer::class, 'sanitize'], $array), ';');
            };

            switch ($type) {
                case 'okupansi':
                    $writeRow($file, ['No', 'Cabang', 'Nomor Kamar', 'Blok', 'Lantai', 'Kategori', 'Kapasitas', 'Terisi', 'Status Kamar', 'Harga Bulanan (Rp)']);
                    $data = Kamar::with('cabangAsrama')->withCount(['penempatans as penghuni_aktif_count' => fn ($query) => $query->where('status', 'active')->whereNotNull('check_in_at')->whereNull('check_out_at')->whereDate('tanggal_mulai', '<=', now()->toDateString())->whereDate('tanggal_akhir', '>', now()->toDateString())])->get();
                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->cabangAsrama->nama ?? 'Cabang belum ditetapkan',
                            $row->nomor_kamar,
                            $row->blok,
                            $row->lantai,
                            strtoupper($row->kategori),
                            $row->kapasitas,
                            $row->penghuni_aktif_count,
                            strtoupper(str_replace('_', ' ', $row->status)),
                            $row->harga_bulanan
                        ]);
                    }
                    break;

                case 'penghuni':
                    $writeRow($file, ['No', 'Nama Penghuni', 'NIK', 'Email', 'No Telepon', 'Cabang', 'Kamar', 'Blok', 'Durasi Sewa', 'Tanggal Masuk']);
                    $data = PenempatanPenghuni::with(['user', 'kamar', 'cabangAsrama', 'reservasi'])->where('status', 'active')->whereNotNull('check_in_at')->whereNull('check_out_at')->whereDate('tanggal_mulai', '<=', now()->toDateString())->whereDate('tanggal_akhir', '>', now()->toDateString())->latest('check_in_at')->get();
                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->user->nama ?? '-',
                            $row->user->nik ?? '-',
                            $row->user->email ?? '-',
                            $row->user->no_telepon ?? '-',
                            $row->cabangAsrama->nama ?? 'Cabang belum ditetapkan',
                            $row->kamar ? 'Kamar ' . $row->kamar->nomor_kamar : '-',
                            $row->kamar->blok ?? '-',
                            $row->reservasi->durasi_sewa ?? '-',
                            $row->check_in_at?->format('Y-m-d') ?? '-'
                        ]);
                    }
                    break;

                case 'reservasi':
                    $writeRow($file, ['No', 'Kode Reservasi', 'Nama Pemohon', 'NIK', 'Cabang', 'Kamar Dialokasikan', 'Durasi Sewa', 'Status', 'Tanggal Pengajuan']);
                    $data = Reservasi::with(['user', 'kamar', 'cabangAsrama'])->latest()->get();
                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->kode_reservasi,
                            $row->user->nama ?? '-',
                            $row->user->nik ?? 'Belum diverifikasi',
                            $row->cabangAsrama->nama ?? 'Cabang belum ditetapkan',
                            $row->kamar ? 'Kamar ' . $row->kamar->nomor_kamar . ' (Blok ' . $row->kamar->blok . ')' : 'Belum ditentukan',
                            $row->durasi_sewa,
                            strtoupper($row->status),
                            \Carbon\Carbon::parse($row->tanggal_pengajuan)->format('Y-m-d')
                        ]);
                    }
                    break;

                case 'pendapatan':
                    $writeRow($file, ['No', 'Kode Pembayaran', 'Nama Penghuni', 'NIK', 'Jumlah (Rp)', 'Metode', 'Tanggal Bayar']);
                    $data = Pembayaran::with('user')->where('status', 'paid')->latest()->get();
                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->kode_pembayaran,
                            $row->user->nama ?? '-',
                            $row->user->nik ?? 'Belum diverifikasi',
                            $row->jumlah_bayar,
                            $row->metode_pembayaran,
                            \Carbon\Carbon::parse($row->updated_at)->format('Y-m-d H:i')
                        ]);
                    }
                    break;

                case 'pemeliharaan':
                    $writeRow($file, ['No', 'Cabang', 'Nomor Kamar', 'Blok', 'Kondisi Status', 'Fasilitas']);
                    $data = Kamar::with('cabangAsrama')->where('status', 'perbaikan')->get();
                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->cabangAsrama->nama ?? 'Cabang belum ditetapkan',
                            $row->nomor_kamar,
                            $row->blok,
                            'DALAM PERBAIKAN',
                            $row->fasilitas ?? '-'
                        ]);
                    }
                    break;

                case 'fasilitas':
                    $writeRow($file, ['No', 'Cabang', 'Nomor Kamar', 'Blok', 'Kategori', 'Daftar Fasilitas']);
                    $data = Kamar::with('cabangAsrama')->get();
                    foreach ($data as $index => $row) {
                        $writeRow($file, [
                            $index + 1,
                            $row->cabangAsrama->nama ?? 'Cabang belum ditetapkan',
                            $row->nomor_kamar,
                            $row->blok,
                            strtoupper($row->kategori),
                            $row->fasilitas ?? 'Standar'
                        ]);
                    }
                    break;

                default:
                    $writeRow($file, ['Error', 'Tipe laporan tidak ditemukan.']);
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
