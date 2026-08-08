<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PembayaranController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Mengambil data reservasi aktif untuk mendapatkan informasi nominal kamar
        $reservasi = Reservasi::with('kamar')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        // Mengambil pembayaran terbaru untuk menampilkan timeline status
        $pembayaranAktif = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->first();

        // Mengambil seluruh riwayat pembayaran milik mahasiswa yang login
        $riwayatPembayaran = Pembayaran::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('penghuni.pembayaran.index', compact('reservasi', 'pembayaranAktif', 'riwayatPembayaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5048',
        ]);

        $user = auth()->user();

        $reservasi = Reservasi::with('kamar')
            ->where('user_id', $user->id)
            ->where('status', 'approved')
            ->latest()
            ->first();

        if (!$reservasi) {
            return redirect()->back()->with('error', 'Anda belum memiliki reservasi kamar yang disetujui.');
        }

        // Upload bukti pembayaran
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        // Buat kode pembayaran unik (misal: PAY-2026-XXXX)
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
}