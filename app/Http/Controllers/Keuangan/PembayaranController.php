<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with(['user', 'reservasi.kamar']);

        if ($request->has('status') && $request->status != 'semua' && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim_nik', 'like', "%{$search}%");
                  });
            });
        }

        $pembayarans = $query->latest()->get();

        return view('keuangan.pembayaran.index', compact('pembayarans'));
    }

    public function verify(Pembayaran $pembayaran)
    {
        $pembayaran->status = 'paid';
        $pembayaran->save();

        return redirect()->back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Pembayaran $pembayaran)
    {
        $request->validate([
            'catatan_keuangan' => 'required|string',
        ]);

        $pembayaran->status = 'rejected';
        $pembayaran->catatan_keuangan = $request->catatan_keuangan;
        $pembayaran->save();

        return redirect()->back()->with('success', 'Pembayaran telah ditolak.');
    }

    public function riwayat(Request $request)
    {
        $query = Pembayaran::with(['user', 'reservasi.kamar'])
            ->whereIn('status', ['paid', 'rejected']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                ->orWhereHas('user', function ($u) use ($search) {
                    $u->where('nama', 'like', "%{$search}%")
                        ->orWhere('nim_nik', 'like', "%{$search}%");
                });
            });
        }

        $riwayats = $query->latest()->get();

        return view('keuangan.riwayat.index', compact('riwayats'));
    }

    /**
     * Cetak kuitansi PDF berukuran A5 Landscape untuk Admin Keuangan
     */
    public function cetakKuitansi($id)
    {
        $pembayaran = Pembayaran::with(['user', 'reservasi.kamar'])
            ->where('status', 'paid')
            ->findOrFail($id);

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