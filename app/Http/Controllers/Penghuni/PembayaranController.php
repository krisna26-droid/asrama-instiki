<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // 1. Ambil data reservasi aktif yang sudah disetujui Admin Asrama
        $reservasi = Reservasi::with('kamar')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        // 2. Ambil transaksi pembayaran terakhir milik pengguna ini
        $pembayaranAktif = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->first();

        // 3. Ambil seluruh riwayat pembayaran untuk tabel bawah
        $riwayatPembayaran = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('penghuni.pembayaran.index', compact(
            'reservasi', 
            'pembayaranAktif', 
            'riwayatPembayaran'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Cek 1: Pastikan user punya reservasi kamar yang disetujui
        $reservasi = Reservasi::with('kamar')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$reservasi) {
            return redirect()->back()->with('error', 'Anda belum memiliki reservasi kamar yang disetujui.');
        }

        // Cek 2: Preventif back-end - Jangan izinkan kirim jika ada pembayaran yang masih pending
        $pembayaranPending = Pembayaran::where('user_id', $user->id)
            ->where('status', 'pending')
            ->exists();

        if ($pembayaranPending) {
            return redirect()->back()->with('error', 'Pembayaran sebelumnya masih dalam proses verifikasi oleh admin.');
        }

        // Cek 3: Validasi Berkas
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5048',
        ]);

        // Simpan File ke Storage
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
        $kodePembayaran = 'PAY-' . date('Y') . '-' . strtoupper(Str::random(4));

        Pembayaran::create([
            'kode_pembayaran'   => $kodePembayaran,
            'reservasi_id'      => $reservasi->id,
            'user_id'           => $user->id,
            'jumlah_bayar'      => $reservasi->kamar->harga_bulanan ?? 850000,
            'metode_pembayaran' => 'Bank Transfer (BNI)',
            'bukti_pembayaran'  => $path,
            'status'            => 'pending',
        ]);

        return redirect()->back()->with('success', 'Bukti pembayaran berhasil diunggah. Menunggu verifikasi admin.');
    }

    /**
     * Cetak kuitansi PDF berukuran A5 Landscape (Optimized)
     */
    public function cetakKuitansi($id)
    {
        $pembayaran = Pembayaran::with(['user', 'reservasi.kamar'])
            ->where('user_id', auth()->id())
            ->where('status', 'paid')
            ->findOrFail($id);

        // Convert Logo ke Base64 agar proses render instant tanpa I/O bottleneck
        $logoPath = public_path('image/Vertical-Logo.png');
        $logoBase64 = '';
        if (file_exists($logoPath)) {
            $logoData = file_get_contents($logoPath);
            $logoBase64 = 'data:image/png;base64,' . base64_encode($logoData);
        }

        $pdf = Pdf::loadView('penghuni.pembayaran.kuitansi_pdf', compact('pembayaran', 'logoBase64'))
            ->setPaper('a5', 'landscape')
            ->setOption([
                'isRemoteEnabled'         => false,
                'isHtml5ParserEnabled'    => true,
                'isFontSubsettingEnabled' => true,
                'dpi'                     => 96,
            ]);

        return $pdf->stream('Kuitansi-' . $pembayaran->kode_pembayaran . '.pdf');
    }
}