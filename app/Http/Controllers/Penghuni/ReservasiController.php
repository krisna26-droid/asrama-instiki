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

        // Mengambil reservasi aktif milik pengguna (pending/approved)
        $reservasiAktif = Reservasi::with(['kamar'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->latest()
            ->first();

        // Ambil daftar kamar yang tersedia
        $kamars = Kamar::where('status', 'tersedia')
            ->whereRaw('kapasitas > terisi')
            ->get();

        return view('penghuni.reservasi.index', compact('reservasiAktif', 'kamars'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Validasi Back-end: Tolak jika sudah punya kamar/reservasi aktif
        if ($user->punyaKamarAktif()) {
            return redirect()->route('penghuni.reservasi.index')
                ->with('error', 'Anda sudah memiliki kamar atau reservasi aktif. Satu penghuni hanya dapat memesan 1 kamar.');
        }

        $request->validate([
            'kamar_id' => 'required|exists:kamars,id',
            'foto_ktp' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'foto_ktm' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $kamar = Kamar::findOrFail($request->kamar_id);
        if ($kamar->terisi >= $kamar->kapasitas || $kamar->status !== 'tersedia') {
            return redirect()->back()->with('error', 'Kamar yang Anda pilih sudah penuh atau tidak tersedia.');
        }

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