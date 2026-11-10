<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Models\User;
use App\Models\Kamar;
use App\Models\PenempatanPenghuni;
use App\Services\ExtensionLifecycleService;
use App\Services\LifecycleLockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::query()->with(['user', 'reservasi', 'perpanjangan.penempatan.kamar']);
        if ($request->filled('status') && $request->input('status') !== 'semua') {
            $query->where('status', $request->string('status')->toString());
        }
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(fn ($builder) => $builder->where('kode_pembayaran', 'like', "%{$term}%")
                ->orWhereHas('user', fn ($user) => $user->where('nama', 'like', "%{$term}%")));
        }
        if ($request->filled('tanggal_dari')) $query->whereDate('created_at', '>=', $request->input('tanggal_dari'));
        if ($request->filled('tanggal_sampai')) $query->whereDate('created_at', '<=', $request->input('tanggal_sampai'));
        $pembayarans = $query->latest()->paginate(15)->withQueryString();
        return view('keuangan.pembayaran.index', compact('pembayarans'));
    }

    public function verify(Pembayaran $pembayaran, LifecycleLockService $locks, ExtensionLifecycleService $extensions)
    {
        $this->decide($pembayaran, $locks, $extensions, 'paid', null);
        return back()->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    public function reject(Request $request, Pembayaran $pembayaran, LifecycleLockService $locks, ExtensionLifecycleService $extensions)
    {
        $data = $request->validate(['catatan_keuangan' => ['required', 'string', 'max:2000']]);
        $this->decide($pembayaran, $locks, $extensions, 'rejected', $data['catatan_keuangan']);
        return back()->with('success', 'Pembayaran ditolak dan catatan keputusan tersimpan.');
    }

    private function decide(Pembayaran $snapshot, LifecycleLockService $locks, ExtensionLifecycleService $extensions, string $status, ?string $note): void
    {
        if ($snapshot->perpanjangan_id) {
            $extensions->decidePayment((int) $snapshot->id, (int) auth()->id(), $status, $note);
            return;
        }

        DB::transaction(function () use ($snapshot, $locks, $status, $note) {
            $paymentSnapshot = Pembayaran::query()->findOrFail($snapshot->id);
            $reservation = $locks->lockReservation((int) $paymentSnapshot->reservasi_id);
            $locks->lockUser((int) $paymentSnapshot->user_id);
            $rooms = $locks->lockAllRooms();
            $placements = $locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), (int) $paymentSnapshot->user_id);
            $locks->lockExtensionsForPlacements($placements->modelKeys());
            $payment = Pembayaran::query()->whereKey($snapshot->id)->lockForUpdate()->firstOrFail();
            if ($payment->status !== 'pending') {
                throw ValidationException::withMessages(['pembayaran' => 'Pembayaran yang sudah diputuskan tidak dapat diproses ulang.']);
            }
            if ($status === 'paid' && $reservation->jumlah_tagihan !== null
                && (int) $payment->jumlah_bayar !== (int) $reservation->jumlah_tagihan) {
                throw ValidationException::withMessages(['pembayaran' => 'Nominal pembayaran tidak sesuai dengan tagihan reservasi. Tolak dengan catatan agar penghuni dapat mengirim ulang.']);
            }
            $payment->status = $status;
            $payment->catatan_keuangan = $note;
            $payment->diputuskan_oleh = auth()->id();
            $payment->diputuskan_at = now();
            $payment->save();
        }, 3);
    }

    public function riwayat(Request $request)
    {
        $query = Pembayaran::query()->with(['user', 'reservasi', 'perpanjangan.penempatan.kamar'])->whereIn('status', ['paid', 'rejected']);
        if ($request->filled('search')) {
            $term = trim($request->string('search')->toString());
            $query->where(fn ($builder) => $builder->where('kode_pembayaran', 'like', "%{$term}%")
                ->orWhereHas('user', fn ($user) => $user->where('nama', 'like', "%{$term}%")));
        }
        if ($request->filled('tanggal_dari')) $query->whereDate('updated_at', '>=', $request->input('tanggal_dari'));
        if ($request->filled('tanggal_sampai')) $query->whereDate('updated_at', '<=', $request->input('tanggal_sampai'));
        $riwayats = $query->latest()->paginate(15)->withQueryString();
        return view('keuangan.riwayat.index', compact('riwayats'));
    }

    public function cetakKuitansi(int $id)
    {
        $pembayaran = Pembayaran::with(['user', 'reservasi', 'perpanjangan.penempatan.kamar'])->where('status', 'paid')->findOrFail($id);
        $logoPath = public_path('image/Vertical-Logo.png');
        $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
        return Pdf::loadView('penghuni.pembayaran.kuitansi_pdf', compact('pembayaran', 'logoBase64'))
            ->setPaper('a5', 'landscape')->setOption(['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true])->stream('Kuitansi-' . $pembayaran->kode_pembayaran . '.pdf');
    }
}
