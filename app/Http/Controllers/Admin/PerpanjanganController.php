<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerpanjanganPenempatan;
use App\Services\ExtensionLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerpanjanganController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status', 'pending')->toString();
        $query = PerpanjanganPenempatan::query()->with(['user', 'penempatan.kamar.cabangAsrama', 'penempatan.cabangAsrama', 'pembayarans']);
        if ($status !== 'semua') {
            $query->where('status', $status);
        }
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(fn ($builder) => $builder->whereHas('user', fn ($user) => $user->where('nama', 'like', "%{$term}%")->orWhere('nik', 'like', "%{$term}%"))
                ->orWhereHas('penempatan.kamar', fn ($room) => $room->where('nomor_kamar', 'like', "%{$term}%")));
        }

        $extensions = $query->latest()->paginate(15)->withQueryString();

        return view('admin.perpanjangan.index', compact('extensions', 'status'));
    }

    public function approve(Request $request, PerpanjanganPenempatan $extension, ExtensionLifecycleService $lifecycle): RedirectResponse
    {
        $data = $request->validate(['catatan_admin' => ['nullable', 'string', 'max:2000']]);
        $lifecycle->approve($extension->id, (int) auth()->id(), $data['catatan_admin'] ?? null);

        return back()->with('success', 'Kapasitas tersedia dan perpanjangan disetujui. Periode tambahan kini ditahan sambil menunggu pembayaran sebelum tanggal akhir.');
    }

    public function reject(Request $request, PerpanjanganPenempatan $extension, ExtensionLifecycleService $lifecycle): RedirectResponse
    {
        $data = $request->validate(['catatan_admin' => ['required', 'string', 'max:2000']]);
        $lifecycle->reject($extension->id, (int) auth()->id(), $data['catatan_admin']);

        return back()->with('success', 'Pengajuan perpanjangan ditolak dan alasan tersimpan.');
    }

    public function cancel(Request $request, PerpanjanganPenempatan $extension, ExtensionLifecycleService $lifecycle): RedirectResponse
    {
        $data = $request->validate(['catatan_admin' => ['required', 'string', 'max:2000']]);
        $lifecycle->cancel($extension->id, (int) auth()->id(), $data['catatan_admin']);

        return back()->with('success', 'Pengajuan perpanjangan dibatalkan dan komitmen kapasitas dilepas.');
    }
}
