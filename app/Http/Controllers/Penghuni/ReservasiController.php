<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Reservasi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservasiController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Cek apakah mahasiswa sudah memiliki reservasi aktif
        $reservasiAktif = Reservasi::with(['kamar'])
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        // Ambil daftar kamar yang tersedia untuk dipilih pada langkah 1
        $kamars = Kamar::where('status', 'tersedia')
            ->whereRaw('kapasitas > terisi')
            ->get();

        return view('penghuni.reservasi.index', compact('reservasiAktif', 'kamars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kamar_id' => 'required|exists:kamars,id',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_ktm' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = auth()->user();

        // Buat kode reservasi unik (misal: RSV-2026-XXXX)
        $kodeReservasi = 'RSV-' . date('Y') . '-' . strtoupper(Str::random(4));

        Reservasi::create([
            'kode_reservasi'    => $kodeReservasi,
            'user_id'           => $user->id,
            'kamar_id'          => $request->kamar_id,
            'tanggal_pengajuan' => now(),
            'durasi_sewa'       => '1 Semester',
            'status'            => 'pending',
        ]);

        return redirect()->route('dashboard')->with('success', 'Pengajuan reservasi kamar berhasil dikirim.');
    }
}