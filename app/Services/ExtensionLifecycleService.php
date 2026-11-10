<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\PerpanjanganPenempatan;
use App\Models\PenempatanPenghuni;
use App\Models\Reservasi;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExtensionLifecycleService
{
    public function __construct(
        private readonly LifecycleLockService $locks,
        private readonly RoomOccupancyService $occupancy,
    ) {}

    public function requestOneMonth(int $placementId, int $userId): PerpanjanganPenempatan
    {
        $snapshot = PenempatanPenghuni::query()->findOrFail($placementId);

        return DB::transaction(function () use ($snapshot, $placementId, $userId) {
            [$reservation, $placements, $extensions, $rooms] = $this->lockContext($snapshot);
            $placement = $placements->firstWhere('id', $placementId);

            if (!$placement || (int) $placement->user_id !== $userId || (int) $reservation->user_id !== $userId) {
                throw ValidationException::withMessages(['perpanjangan' => 'Penempatan ini bukan milik akun Anda.']);
            }
            $this->assertExtendable($placement, $reservation);

            if ($extensions->contains(fn ($extension) => (int) $extension->penempatan_id === $placementId
                && in_array($extension->status, ['pending', 'approved'], true))) {
                throw ValidationException::withMessages(['perpanjangan' => 'Masih ada pengajuan perpanjangan yang belum selesai untuk penempatan ini.']);
            }

            $room = $rooms->firstWhere('id', $placement->kamar_id);
            if (!$room || (int) $room->harga_bulanan <= 0) {
                throw ValidationException::withMessages(['perpanjangan' => 'Tarif kamar belum dapat digunakan untuk membuat tagihan perpanjangan. Hubungi Admin Asrama.']);
            }

            $oldEnd = $placement->tanggal_akhir->toDateString();
            $newEnd = CarbonImmutable::createFromFormat('!Y-m-d', $oldEnd)->addMonthNoOverflow()->toDateString();

            return PerpanjanganPenempatan::create([
                'penempatan_id' => $placement->id,
                'user_id' => $userId,
                'tanggal_akhir_sebelum' => $oldEnd,
                'tanggal_akhir_diajukan' => $newEnd,
                'durasi_bulan' => 1,
                'tarif_bulanan' => (int) $room->harga_bulanan,
                'jumlah_tagihan' => (int) $room->harga_bulanan,
                'status' => 'pending',
                'diajukan_at' => now(),
            ]);
        }, 3);
    }

    public function approve(int $extensionId, int $actorId, ?string $note = null): PerpanjanganPenempatan
    {
        $extensionSnapshot = PerpanjanganPenempatan::query()->findOrFail($extensionId);
        $placementSnapshot = PenempatanPenghuni::query()->findOrFail($extensionSnapshot->penempatan_id);

        return DB::transaction(function () use ($extensionId, $placementSnapshot, $actorId, $note) {
            [$reservation, $placements, $extensions, $rooms] = $this->lockContext($placementSnapshot);
            $placement = $placements->firstWhere('id', $placementSnapshot->id);
            $extension = $extensions->firstWhere('id', $extensionId);

            if (!$extension || !$placement || $extension->status !== 'pending') {
                throw ValidationException::withMessages(['perpanjangan' => 'Pengajuan ini sudah diputuskan atau tidak ditemukan.']);
            }
            $this->assertExtendable($placement, $reservation);
            if ($extension->tanggal_akhir_sebelum->toDateString() !== $placement->tanggal_akhir->toDateString()) {
                throw ValidationException::withMessages(['perpanjangan' => 'Tanggal penempatan berubah sejak pengajuan dibuat. Pengajuan ini tidak dapat disetujui.']);
            }

            $room = $rooms->firstWhere('id', $placement->kamar_id);
            if (!$room || !$this->occupancy->canFit(
                $room,
                $placements,
                $extension->tanggal_akhir_sebelum->toDateString(),
                $extension->tanggal_akhir_diajukan->toDateString(),
            )) {
                throw ValidationException::withMessages(['perpanjangan' => 'Kapasitas kamar tidak tersedia untuk periode perpanjangan yang diminta.']);
            }

            $extension->status = 'approved';
            $extension->diputuskan_oleh = $actorId;
            $extension->diputuskan_at = now();
            $extension->catatan_admin = $note;
            $extension->save();

            return $extension;
        }, 3);
    }

    public function reject(int $extensionId, int $actorId, string $reason): PerpanjanganPenempatan
    {
        $extensionSnapshot = PerpanjanganPenempatan::query()->findOrFail($extensionId);
        $placementSnapshot = PenempatanPenghuni::query()->findOrFail($extensionSnapshot->penempatan_id);

        return DB::transaction(function () use ($extensionId, $placementSnapshot, $actorId, $reason) {
            [, , $extensions] = $this->lockContext($placementSnapshot);
            $extension = $extensions->firstWhere('id', $extensionId);
            if (!$extension || $extension->status !== 'pending') {
                throw ValidationException::withMessages(['perpanjangan' => 'Hanya pengajuan yang masih menunggu yang dapat ditolak.']);
            }

            $extension->status = 'rejected';
            $extension->diputuskan_oleh = $actorId;
            $extension->diputuskan_at = now();
            $extension->catatan_admin = $reason;
            $extension->save();

            return $extension;
        }, 3);
    }

    public function cancel(int $extensionId, int $actorId, string $reason): PerpanjanganPenempatan
    {
        $extensionSnapshot = PerpanjanganPenempatan::query()->findOrFail($extensionId);
        $placementSnapshot = PenempatanPenghuni::query()->findOrFail($extensionSnapshot->penempatan_id);

        return DB::transaction(function () use ($extensionId, $placementSnapshot, $actorId, $reason) {
            [, , $extensions] = $this->lockContext($placementSnapshot);
            $extension = $extensions->firstWhere('id', $extensionId);
            if (!$extension || !in_array($extension->status, ['pending', 'approved'], true)) {
                throw ValidationException::withMessages(['perpanjangan' => 'Hanya pengajuan terbuka yang dapat dibatalkan.']);
            }
            if ($extension->pembayarans()->whereIn('status', ['pending', 'paid'])->exists()) {
                throw ValidationException::withMessages(['perpanjangan' => 'Tuntaskan keputusan pembayaran yang sedang menunggu terlebih dahulu; pembayaran lunas tidak dapat dibatalkan.']);
            }

            $extension->status = 'cancelled';
            $extension->diputuskan_oleh = $actorId;
            $extension->diputuskan_at = now();
            $extension->catatan_admin = $reason;
            $extension->save();

            return $extension;
        }, 3);
    }

    public function createPayment(int $extensionId, int $userId, string $proofPath): Pembayaran
    {
        $extensionSnapshot = PerpanjanganPenempatan::query()->findOrFail($extensionId);
        $placementSnapshot = PenempatanPenghuni::query()->findOrFail($extensionSnapshot->penempatan_id);

        return DB::transaction(function () use ($extensionId, $userId, $proofPath, $placementSnapshot) {
            [$reservation, $placements, $extensions] = $this->lockContext($placementSnapshot);
            $extension = $extensions->firstWhere('id', $extensionId);
            $placement = $placements->firstWhere('id', $placementSnapshot->id);

            if (!$extension || !$placement || (int) $extension->user_id !== $userId || (int) $reservation->user_id !== $userId) {
                throw ValidationException::withMessages(['perpanjangan' => 'Pengajuan perpanjangan ini bukan milik Anda.']);
            }
            if ($extension->status !== 'approved' || $placement->status !== 'active' || $placement->check_out_at) {
                throw ValidationException::withMessages(['perpanjangan' => 'Perpanjangan belum disetujui atau sudah tidak dapat dibayar.']);
            }
            if ($placement->tanggal_akhir->toDateString() !== $extension->tanggal_akhir_sebelum->toDateString()
                || now()->toDateString() >= $extension->tanggal_akhir_sebelum->toDateString()) {
                throw ValidationException::withMessages(['perpanjangan' => 'Batas pembayaran perpanjangan telah lewat. Penghuni tidak diperpanjang otomatis.']);
            }

            if (Pembayaran::query()->where('perpanjangan_id', $extension->id)->whereIn('status', ['pending', 'paid'])->exists()) {
                throw ValidationException::withMessages(['pembayaran' => 'Bukti perpanjangan sudah dikirim atau sudah lunas.']);
            }

            $payment = new Pembayaran();
            $payment->kode_pembayaran = 'PAY-' . now()->format('Y') . '-' . strtoupper(bin2hex(random_bytes(4)));
            $payment->reservasi_id = $reservation->id;
            $payment->perpanjangan_id = $extension->id;
            $payment->user_id = $userId;
            $payment->jumlah_bayar = $extension->jumlah_tagihan;
            $payment->metode_pembayaran = 'Bank Transfer (BNI)';
            $payment->bukti_pembayaran = $proofPath;
            $payment->status = 'pending';
            $payment->save();

            return $payment;
        }, 3);
    }

    public function decidePayment(int $paymentId, int $actorId, string $status, ?string $note = null): void
    {
        $paymentSnapshot = Pembayaran::query()->findOrFail($paymentId);
        if (!$paymentSnapshot->perpanjangan_id) {
            throw ValidationException::withMessages(['pembayaran' => 'Pembayaran ini bukan pembayaran perpanjangan.']);
        }
        $extensionSnapshot = PerpanjanganPenempatan::query()->findOrFail($paymentSnapshot->perpanjangan_id);
        $placementSnapshot = PenempatanPenghuni::query()->findOrFail($extensionSnapshot->penempatan_id);

        DB::transaction(function () use ($paymentId, $actorId, $status, $note, $placementSnapshot, $extensionSnapshot) {
            [$reservation, $placements, $extensions] = $this->lockContext($placementSnapshot);
            $placement = $placements->firstWhere('id', $placementSnapshot->id);
            $extension = $extensions->firstWhere('id', $extensionSnapshot->id);
            $payment = Pembayaran::query()->whereKey($paymentId)->lockForUpdate()->firstOrFail();

            if (!$extension || !$placement || $payment->status !== 'pending') {
                throw ValidationException::withMessages(['pembayaran' => 'Pembayaran yang sudah diputuskan tidak dapat diproses ulang.']);
            }
            if ((int) $payment->perpanjangan_id !== (int) $extension->id
                || (int) $payment->reservasi_id !== (int) $reservation->id
                || (int) $payment->user_id !== (int) $extension->user_id) {
                throw ValidationException::withMessages(['pembayaran' => 'Relasi pembayaran perpanjangan tidak konsisten.']);
            }
            if ((int) $payment->jumlah_bayar !== (int) $extension->jumlah_tagihan) {
                throw ValidationException::withMessages(['pembayaran' => 'Nominal tidak sesuai dengan tagihan perpanjangan.']);
            }

            if ($status === 'paid') {
                if ($extension->status !== 'approved' || $placement->status !== 'active' || $placement->check_out_at
                    || $placement->tanggal_akhir->toDateString() !== $extension->tanggal_akhir_sebelum->toDateString()
                    || $payment->created_at->toDateString() >= $extension->tanggal_akhir_sebelum->toDateString()) {
                    throw ValidationException::withMessages(['pembayaran' => 'Perpanjangan sudah kedaluwarsa atau tidak lagi memenuhi syarat untuk dibayar.']);
                }

                $room = $placement->kamar;
                if (!$room || !$this->occupancy->canFit(
                    $room,
                    $placements,
                    $extension->tanggal_akhir_sebelum->toDateString(),
                    $extension->tanggal_akhir_diajukan->toDateString(),
                    $extension->id,
                )) {
                    throw ValidationException::withMessages(['pembayaran' => 'Kapasitas kamar untuk periode perpanjangan tidak lagi tersedia.']);
                }
            }

            $payment->status = $status;
            $payment->catatan_keuangan = $note;
            $payment->diputuskan_oleh = $actorId;
            $payment->diputuskan_at = now();
            $payment->save();

            if ($status === 'paid') {
                $placement->tanggal_akhir = $extension->tanggal_akhir_diajukan;
                $placement->save();
                $extension->status = 'completed';
                $extension->dibayar_at = now();
                $extension->save();
            } elseif (now()->toDateString() >= $extension->tanggal_akhir_sebelum->toDateString()) {
                $extension->status = 'expired';
                $extension->catatan_admin = trim(($extension->catatan_admin ? $extension->catatan_admin . "\n" : '') . 'Tidak lunas sebelum akhir periode tinggal.');
                $extension->save();
                $this->completePlacementIfEnded($reservation, $placement, $placements);
            }
        }, 3);
    }

    public function finalizeEndedPlacement(int $placementId): bool
    {
        $snapshot = PenempatanPenghuni::query()->find($placementId);
        if (!$snapshot) {
            return false;
        }

        return DB::transaction(function () use ($snapshot, $placementId) {
            [$reservation, $placements, $extensions] = $this->lockContext($snapshot);
            $placement = $placements->firstWhere('id', $placementId);
            if (!$placement || $placement->status !== 'active' || !$placement->check_in_at || $placement->check_out_at
                || $placement->tanggal_akhir->toDateString() > now()->toDateString()) {
                return false;
            }

            if ($placement->transferKe()->whereIn('status', ['scheduled', 'no_show'])->exists()) {
                return false;
            }

            $placementExtensions = $extensions->where('penempatan_id', $placement->id);
            foreach ($placementExtensions as $extension) {
                if ($extension->status === 'pending') {
                    $extension->status = 'rejected';
                    $extension->diputuskan_at = now();
                    $extension->catatan_admin = 'Ditolak sistem karena masa tinggal berakhir sebelum pengajuan diputuskan.';
                    $extension->save();
                }
                if ($extension->status === 'approved') {
                    $hasPendingPayment = Pembayaran::query()->where('perpanjangan_id', $extension->id)
                        ->where('status', 'pending')
                        ->whereDate('created_at', '<', $extension->tanggal_akhir_sebelum->toDateString())
                        ->exists();
                    if ($hasPendingPayment) {
                        return false;
                    }

                    $extension->status = 'expired';
                    $extension->diputuskan_at = now();
                    $extension->catatan_admin = trim(($extension->catatan_admin ? $extension->catatan_admin . "\n" : '') . 'Tidak ada pembayaran lunas sebelum akhir periode tinggal.');
                    $extension->save();
                }
            }

            $placement->status = 'completed';
            $placement->save();
            $this->completeReservationIfEnded($reservation);

            return true;
        }, 3);
    }

    /** @return array{0: Reservasi, 1: \Illuminate\Support\Collection, 2: \Illuminate\Support\Collection, 3: \Illuminate\Support\Collection} */
    private function lockContext(PenempatanPenghuni $snapshot): array
    {
        $reservation = $this->locks->lockReservation((int) $snapshot->reservasi_id);
        $userId = (int) $snapshot->user_id;
        $this->locks->lockUser($userId);
        $rooms = $this->locks->lockAllRooms();
        $placements = $this->locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), $userId);
        $extensions = $this->locks->lockExtensionsForPlacements($placements->modelKeys());

        return [$reservation, $placements, $extensions, $rooms];
    }

    private function assertExtendable(PenempatanPenghuni $placement, Reservasi $reservation): void
    {
        $today = now()->toDateString();
        if ($reservation->status !== 'approved' || $placement->status !== 'active' || !$placement->check_in_at
            || $placement->check_out_at || $today < $placement->tanggal_mulai->toDateString()
            || $today >= $placement->tanggal_akhir->toDateString()) {
            throw ValidationException::withMessages(['perpanjangan' => 'Perpanjangan hanya dapat diajukan untuk penempatan aktif sebelum tanggal akhir.']);
        }
    }

    private function completePlacementIfEnded(Reservasi $reservation, PenempatanPenghuni $placement, $placements): void
    {
        if ($placement->tanggal_akhir->toDateString() > now()->toDateString()) {
            return;
        }

        $placement->status = 'completed';
        $placement->save();
        foreach ($placements as $sibling) {
            if ((int) $sibling->id !== (int) $placement->id && $sibling->status === 'scheduled'
                && $sibling->tanggal_mulai->toDateString() >= now()->toDateString()) {
                $sibling->status = 'cancelled';
                $sibling->save();
            }
        }
        $this->completeReservationIfEnded($reservation);
    }

    private function completeReservationIfEnded(Reservasi $reservation): void
    {
        $allPlacements = PenempatanPenghuni::query()->where('reservasi_id', $reservation->id)->get();
        if ($allPlacements->isNotEmpty() && $allPlacements->every(fn ($item) => in_array($item->status, ['completed', 'cancelled'], true))) {
            $reservation->status = 'completed';
            $reservation->save();
        }
    }
}
