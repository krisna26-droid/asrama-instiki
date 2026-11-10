<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\PenempatanPenghuni;
use App\Services\ExtensionLifecycleService;
use Illuminate\Http\RedirectResponse;

class PerpanjanganController extends Controller
{
    public function store(PenempatanPenghuni $placement, ExtensionLifecycleService $extensions): RedirectResponse
    {
        $extensions->requestOneMonth($placement->id, (int) auth()->id());

        return back()->with('success', 'Pengajuan perpanjangan 1 bulan berhasil dikirim. Kamar baru diperpanjang setelah kapasitas disetujui Admin Asrama dan pembayaran diverifikasi Admin Keuangan.');
    }
}
