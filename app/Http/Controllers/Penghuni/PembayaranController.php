<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Reservasi;
use App\Services\LifecycleLockService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class PembayaranController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $reservasi = Reservasi::query()->with(['kamar', 'penempatans.kamar'])->where('user_id', $userId)->where('status', 'approved')->whereNotNull('kamar_id')
            ->whereHas('penempatans', fn ($query) => $query->whereIn('status', ['scheduled', 'active']))
            ->latest()->first();
        if ($reservasi && !$reservasi->kamar) {
            $reservasi = null;
        }
        $pembayaranAktif = $reservasi
            ? Pembayaran::query()->where('reservasi_id', $reservasi->id)->latest()->first()
            : null;
        $riwayatPembayaran = Pembayaran::query()->where('user_id', $userId)->latest()->paginate(10)->withQueryString();
        return view('penghuni.pembayaran.index', compact('reservasi', 'pembayaranAktif', 'riwayatPembayaran'));
    }

    public function store(Request $request, LifecycleLockService $locks)
    {
        $request->validate(['bukti_pembayaran' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120']]);
        $userId = (int) $request->user()->id;
        $reservationSnapshot = Reservasi::query()->where('user_id', $userId)->where('status', 'approved')->whereNotNull('kamar_id')
            ->whereHas('penempatans', fn ($query) => $query->whereIn('status', ['scheduled', 'active']))
            ->latest()->first();
        if (!$reservationSnapshot) {
            return back()->with('error', 'Anda belum memiliki reservasi yang disetujui.');
        }
        if ($reservationSnapshot->jumlah_tagihan === null) {
            return back()->with('error', 'Tagihan reservasi ini belum memiliki rincian durasi dan tarif yang terverifikasi. Hubungi Admin Asrama sebelum mengirim pembayaran.');
        }

        $file = $request->file('bukti_pembayaran');
        if (! $file || ! $file->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages(['bukti_pembayaran' => 'File bukti pembayaran gagal diterima. Silakan pilih ulang file.']);
        }

        $path = $file->store('bukti-pembayaran', 'local');
        if (! is_string($path) || ! Storage::disk('local')->exists($path)) {
            throw \Illuminate\Validation\ValidationException::withMessages(['bukti_pembayaran' => 'File bukti pembayaran gagal disimpan. Silakan coba kembali.']);
        }
        try {
            DB::transaction(function () use ($reservationSnapshot, $userId, $path, $locks) {
                $reservation = $locks->lockReservation((int) $reservationSnapshot->id);
                if ((int) $reservation->user_id !== $userId || $reservation->status !== 'approved') {
                    throw new \RuntimeException('Reservasi tidak lagi memenuhi syarat pembayaran.');
                }
                $locks->lockUser($userId);
                $rooms = $locks->lockAllRooms();
                $placements = $locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), $userId);

                if ($reservation->jumlah_tagihan === null) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'pembayaran' => 'Tagihan belum memiliki durasi dan tarif yang terverifikasi. Hubungi Admin Asrama.',
                    ]);
                }

                if (Pembayaran::query()->where('reservasi_id', $reservation->id)->whereIn('status', ['pending', 'paid'])->exists()) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'pembayaran' => 'Pembayaran reservasi ini sudah dikirim atau sudah lunas.',
                    ]);
                }

                $hasPlacement = $placements->contains(fn ($placement) => (int) $placement->reservasi_id === (int) $reservation->id
                    && in_array($placement->status, ['scheduled', 'active'], true));
                $room = $rooms->firstWhere('id', $reservation->kamar_id);
                if (!$hasPlacement || !$reservation->kamar_id || !$room) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'pembayaran' => 'Pembayaran belum dapat diajukan karena placement kamar belum tersedia. Hubungi Admin Asrama.',
                    ]);
                }

                $payment = new Pembayaran();
                $payment->kode_pembayaran = 'PAY-' . now()->format('Y') . '-' . strtoupper(Str::random(8));
                $payment->reservasi_id = $reservation->id;
                $payment->user_id = $userId;
                $payment->jumlah_bayar = $reservation->jumlah_tagihan;
                $payment->metode_pembayaran = 'Bank Transfer (BNI)';
                $payment->bukti_pembayaran = $path;
                $payment->status = 'pending';
                $payment->save();
            }, 3);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);
            throw $exception;
        }

        return back()->with('success', 'Bukti pembayaran tersimpan secara privat dan menunggu verifikasi.');
    }

    public function cetakKuitansi(int $id)
    {
        $pembayaran = Pembayaran::with(['user', 'reservasi.kamar'])
            ->where('user_id', auth()->id())->where('status', 'paid')->findOrFail($id);
        $logoPath = public_path('image/Vertical-Logo.png');
        $logoBase64 = is_file($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : '';
        return Pdf::loadView('penghuni.pembayaran.kuitansi_pdf', compact('pembayaran', 'logoBase64'))
            ->setPaper('a5', 'landscape')->setOption(['isRemoteEnabled' => false, 'isHtml5ParserEnabled' => true])->stream('Kuitansi-' . $pembayaran->kode_pembayaran . '.pdf');
    }
}
