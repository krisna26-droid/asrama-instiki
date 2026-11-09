<?php

namespace App\Services;

use App\Models\PenempatanPenghuni;
use App\Models\Reservasi;
use App\Models\Kamar;
use App\Models\Pembayaran;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\CarbonImmutable;

class ReservationLifecycleService
{
    public function __construct(
        private readonly LifecycleLockService $locks,
        private readonly RoomOccupancyService $occupancy,
    ) {}

    public function approve(int $reservationId, string $start, ?int $durationMonths = null): PenempatanPenghuni
    {
        return $this->allocateReservation($reservationId, $start, null, 'pending', $durationMonths);
    }

    public function verifyLegacy(int $reservationId, string $start, string $end): PenempatanPenghuni
    {
        return $this->allocateReservation($reservationId, $start, $end, 'legacy_unverified');
    }

    private function allocateReservation(int $reservationId, string $start, ?string $end, string $expectedStatus, ?int $durationMonths = null): PenempatanPenghuni
    {
        return DB::transaction(function () use ($reservationId, $start, $end, $expectedStatus, $durationMonths) {
            $reservation = $this->locks->lockReservation($reservationId);
            if ($reservation->status !== $expectedStatus || $reservation->penempatans()->exists()) {
                throw ValidationException::withMessages(['reservation' => 'Reservasi ini tidak memenuhi syarat untuk dialokasikan.']);
            }

            $user = $this->locks->lockUser((int) $reservation->user_id);
            if (!in_array($user->jenis_kelamin, ['putra', 'putri'], true)) {
                throw ValidationException::withMessages(['reservation' => 'Data jenis kelamin penghuni belum ditetapkan Admin Asrama.']);
            }

            $rooms = $this->locks->lockAllRooms();
            $placements = $this->locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), (int) $user->id);

            $months = null;
            if ($expectedStatus === 'pending') {
                $months = $reservation->durasi_bulan ?? $durationMonths;
                if (!is_int($months) || $months < 1 || $months > 12) {
                    throw ValidationException::withMessages(['durasi_bulan' => 'Durasi reservasi harus antara 1 sampai 12 bulan.']);
                }
                if ($reservation->durasi_bulan !== null && $durationMonths !== null && $durationMonths !== $reservation->durasi_bulan) {
                    throw ValidationException::withMessages(['durasi_bulan' => 'Durasi yang dikirim berbeda dari durasi yang diajukan penghuni.']);
                }

                $startDate = CarbonImmutable::createFromFormat('!Y-m-d', $start);
                if (!$startDate || $startDate->format('Y-m-d') !== $start) {
                    throw ValidationException::withMessages(['tanggal_mulai' => 'Tanggal mulai tidak valid.']);
                }
                $end = $startDate->addMonthsNoOverflow($months)->toDateString();
            }

            if ($end === null || $start >= $end) {
                throw ValidationException::withMessages(['tanggal_akhir' => 'Tanggal akhir periode harus setelah tanggal mulai.']);
            }

            foreach ($placements->where('user_id', $user->id) as $existing) {
                if (in_array($existing->status, ['scheduled', 'active', 'no_show'], true)
                    && $existing->tanggal_mulai->toDateString() < $end
                    && $this->effectiveEnd($existing) > $start) {
                    throw ValidationException::withMessages(['reservation' => 'Penghuni sudah memiliki penempatan pada periode tersebut.']);
                }
            }

            $eligible = $rooms->filter(function ($room) use ($user, $placements, $start, $end) {
                return $room->kategori === $user->jenis_kelamin
                    && $room->status !== 'perbaikan'
                    && $this->occupancy->canFit($room, $placements, $start, $end);
            })->values();

            if ($eligible->isEmpty()) {
                throw ValidationException::withMessages(['reservation' => 'Tidak ada kamar dengan kategori dan kapasitas yang sesuai untuk periode tersebut.']);
            }

            $room = $eligible[random_int(0, $eligible->count() - 1)];
            $monthlyRate = (int) $room->harga_bulanan;
            $totalBill = null;
            if ($months !== null) {
                if ($monthlyRate < 0 || $monthlyRate > intdiv(PHP_INT_MAX, $months)) {
                    throw ValidationException::withMessages(['reservasi' => 'Nominal tagihan berada di luar batas yang didukung.']);
                }
                $totalBill = $monthlyRate * $months;
            }

            $placement = PenempatanPenghuni::create([
                'reservasi_id' => $reservation->id,
                'user_id' => $user->id,
                'kamar_id' => $room->id,
                'tanggal_mulai' => $start,
                'tanggal_akhir' => $end,
                'status' => 'scheduled',
            ]);

            if ($expectedStatus === 'legacy_unverified' && $reservation->kamar_id && !$reservation->kamar_legacy_id) {
                $reservation->kamar_legacy_id = $reservation->kamar_id;
            }
            $reservation->kamar_id = $room->id;
            $reservation->status = 'approved';
            if ($months !== null) {
                $reservation->durasi_bulan = $months;
                $reservation->durasi_sewa = $months . ' bulan';
                $reservation->tarif_bulanan_disetujui = $monthlyRate;
                $reservation->jumlah_tagihan = $totalBill;
                $reservation->tanggal_mulai_disetujui = $start;
                $reservation->tanggal_akhir_disetujui = $end;
            }
            $reservation->save();

            return $placement;
        }, 3);
    }

    public function checkIn(int $placementId, int $actorId): PenempatanPenghuni
    {
        return DB::transaction(function () use ($placementId, $actorId) {
            $placementSnapshot = PenempatanPenghuni::query()->findOrFail($placementId);
            $reservation = $this->locks->lockReservation((int) $placementSnapshot->reservasi_id);
            $this->locks->lockUser((int) $placementSnapshot->user_id);
            $this->locks->lockAllRooms();
            $placement = PenempatanPenghuni::query()->whereKey($placementId)->lockForUpdate()->firstOrFail();

            if ($placement->status !== 'scheduled' || $placement->check_in_at || $reservation->status !== 'approved') {
                throw ValidationException::withMessages(['placement' => 'Penempatan ini tidak dapat check-in.']);
            }

            if (!Pembayaran::query()->where('reservasi_id', $reservation->id)->where('status', 'paid')->exists()) {
                throw ValidationException::withMessages(['placement' => 'Check-in hanya dapat dicatat setelah pembayaran reservasi diverifikasi lunas oleh Admin Keuangan.']);
            }

            $placement->status = 'active';
            $placement->check_in_at = now();
            $placement->check_in_oleh = $actorId;
            $placement->save();

            return $placement;
        }, 3);
    }

    public function checkOut(int $placementId, int $actorId, string $reason): PenempatanPenghuni
    {
        return DB::transaction(function () use ($placementId, $actorId, $reason) {
            $snapshot = PenempatanPenghuni::query()->findOrFail($placementId);
            $reservation = $this->locks->lockReservation((int) $snapshot->reservasi_id);
            $this->locks->lockUser((int) $snapshot->user_id);
            $this->locks->lockAllRooms();
            $placements = PenempatanPenghuni::query()
                ->where('reservasi_id', $reservation->id)
                ->orderBy('id')->lockForUpdate()->get();
            $placement = $placements->firstWhere('id', $placementId);

            if (!$placement || $placement->status !== 'active' || !$placement->check_in_at) {
                throw ValidationException::withMessages(['placement' => 'Hanya penempatan aktif yang dapat checkout.']);
            }

            $checkoutAt = now();
            $placement->status = 'completed';
            $placement->check_out_at = $checkoutAt;
            $placement->check_out_oleh = $actorId;
            $placement->catatan_lifecycle = trim(($placement->catatan_lifecycle ? $placement->catatan_lifecycle."\n" : '') . 'Checkout: ' . $reason);
            $placement->save();

            foreach ($placements as $future) {
                if ($future->id !== $placement->id && $future->status === 'scheduled' && $future->tanggal_mulai->greaterThanOrEqualTo($checkoutAt->toDateString())) {
                    $future->status = 'cancelled';
                    $future->catatan_lifecycle = trim(($future->catatan_lifecycle ? $future->catatan_lifecycle."\n" : '') . 'Dibatalkan akibat checkout penghuni.');
                    $future->save();
                }
            }

            if ($placements->every(fn ($entry) => in_array($entry->status, ['completed', 'cancelled'], true))) {
                $reservation->status = 'completed';
                $reservation->save();
            }

            return $placement;
        }, 3);
    }

    public function transferDirect(int $sourcePlacementId, int $targetRoomId, int $actorId, string $reason): PenempatanPenghuni
    {
        $snapshot = PenempatanPenghuni::query()->findOrFail($sourcePlacementId);

        return DB::transaction(function () use ($snapshot, $sourcePlacementId, $targetRoomId, $actorId, $reason) {
            $reservation = $this->locks->lockReservation((int) $snapshot->reservasi_id);
            $user = $this->locks->lockUser((int) $snapshot->user_id);
            $rooms = $this->locks->lockAllRooms();
            $placements = $this->locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), (int) $user->id);
            $source = $placements->firstWhere('id', $sourcePlacementId);
            $target = $rooms->firstWhere('id', $targetRoomId);

            if (!$source || $source->status !== 'active' || !$source->check_in_at || $source->check_out_at
                || $reservation->status !== 'approved') {
                throw ValidationException::withMessages(['placement' => 'Hanya penempatan aktif yang dapat dipindahkan.']);
            }
            if (!$target || (int) $target->id === (int) $source->kamar_id
                || $target->kategori !== $user->jenis_kelamin
                || !$target->is_active || $target->status === 'perbaikan') {
                throw ValidationException::withMessages(['kamar_id' => 'Kamar tujuan tidak aktif atau tidak sesuai kategori penghuni.']);
            }

            $now = now();
            $transferDate = $now->toDateString();
            $plannedEnd = $source->tanggal_akhir->toDateString();
            if ($transferDate < $source->tanggal_mulai->toDateString() || $transferDate >= $plannedEnd) {
                throw ValidationException::withMessages(['placement' => 'Transfer hanya dapat dilakukan selama periode placement masih berjalan.']);
            }
            if (!$this->occupancy->canFit($target, $placements, $transferDate, $plannedEnd)) {
                throw ValidationException::withMessages(['kamar_id' => 'Kapasitas kamar tujuan tidak cukup untuk sisa periode.']);
            }

            $source->tanggal_akhir = $transferDate;
            $source->status = 'completed';
            $source->check_out_at = $now;
            $source->check_out_oleh = $actorId;
            $source->catatan_lifecycle = trim(($source->catatan_lifecycle ? $source->catatan_lifecycle . "\n" : '') . 'Transfer langsung: ' . $reason);
            $source->save();

            foreach ($placements->where('user_id', $user->id) as $existing) {
                if ((int) $existing->id === (int) $source->id || !in_array($existing->status, ['scheduled', 'active', 'no_show'], true)) {
                    continue;
                }
                if ($existing->tanggal_mulai->toDateString() < $plannedEnd && $this->effectiveEnd($existing) > $transferDate) {
                    throw ValidationException::withMessages(['placement' => 'Penghuni sudah memiliki penempatan lain yang bertumpang tindih dengan sisa periode.']);
                }
            }

            return PenempatanPenghuni::create([
                'reservasi_id' => $reservation->id,
                'user_id' => $user->id,
                'kamar_id' => $target->id,
                'transfer_dari_id' => $source->id,
                'tanggal_akhir_asal_sebelum_transfer' => $plannedEnd,
                'tanggal_mulai' => $transferDate,
                'tanggal_akhir' => $plannedEnd,
                'status' => 'active',
                'check_in_at' => $now,
                'check_in_oleh' => $actorId,
                'catatan_lifecycle' => 'Transfer langsung dari placement #' . $source->id . ': ' . $reason,
            ]);
        }, 3);
    }

    private function effectiveEnd(PenempatanPenghuni $placement): string
    {
        if ($placement->status === 'no_show') {
            return '9999-12-31';
        }
        $planned = $placement->tanggal_akhir->toDateString();
        return $placement->check_out_at ? min($planned, $placement->check_out_at->toDateString()) : $planned;
    }
}
