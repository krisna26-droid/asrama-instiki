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

    public function approve(int $reservationId, string $start, int $branchId, ?int $durationMonths = null): PenempatanPenghuni
    {
        return $this->allocateReservation($reservationId, $start, null, 'pending', $branchId, $durationMonths);
    }

    public function verifyLegacy(int $reservationId, string $start, string $end, int $branchId): PenempatanPenghuni
    {
        return $this->allocateReservation($reservationId, $start, $end, 'legacy_unverified', $branchId);
    }

    private function allocateReservation(int $reservationId, string $start, ?string $end, string $expectedStatus, int $branchId, ?int $durationMonths = null): PenempatanPenghuni
    {
        return DB::transaction(function () use ($reservationId, $start, $end, $expectedStatus, $branchId, $durationMonths) {
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
            $this->locks->lockExtensionsForPlacements($placements->modelKeys());
            $branch = $this->locks->lockBranches([$branchId])->firstWhere('id', $branchId);
            if (!$branch || !$branch->is_active) {
                throw ValidationException::withMessages(['cabang_asrama_id' => 'Pilih cabang asrama yang masih aktif.']);
            }

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

            $eligible = $rooms->filter(function ($room) use ($user, $placements, $start, $end, $branchId) {
                return $room->kategori === $user->jenis_kelamin
                    && (int) $room->cabang_asrama_id === $branchId
                    && $room->status !== 'perbaikan'
                    && $this->occupancy->canFit($room, $placements, $start, $end);
            })->values();

            if ($eligible->isEmpty()) {
                throw ValidationException::withMessages(['reservation' => 'Tidak ada kamar tersedia pada cabang yang dipilih dengan kategori dan kapasitas yang sesuai untuk periode tersebut.']);
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
                'cabang_asrama_id' => $branchId,
                'tanggal_mulai' => $start,
                'tanggal_akhir' => $end,
                'status' => 'scheduled',
            ]);

            if ($expectedStatus === 'legacy_unverified' && $reservation->kamar_id && !$reservation->kamar_legacy_id) {
                $reservation->kamar_legacy_id = $reservation->kamar_id;
            }
            $reservation->kamar_id = $room->id;
            $reservation->cabang_asrama_id = $branchId;
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
            $relatedReservationIds = PenempatanPenghuni::query()
                ->where(function ($query) use ($placementSnapshot) {
                    $query->where('user_id', $placementSnapshot->user_id)
                        ->orWhere('kamar_id', $placementSnapshot->kamar_id);
                })
                ->pluck('reservasi_id')
                ->push((int) $placementSnapshot->reservasi_id)
                ->merge(Reservasi::query()->where('user_id', $placementSnapshot->user_id)->pluck('id'))
                ->all();
            $reservations = $this->locks->lockReservations($relatedReservationIds);
            $reservation = $reservations->firstWhere('id', (int) $placementSnapshot->reservasi_id);
            if (!$reservation) {
                throw ValidationException::withMessages(['placement' => 'Reservasi penempatan tidak ditemukan.']);
            }

            $this->locks->lockUser((int) $placementSnapshot->user_id);
            $rooms = $this->locks->lockAllRooms();
            $placements = $this->locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), (int) $placementSnapshot->user_id);
            $extensions = $this->locks->lockExtensionsForPlacements($placements->modelKeys());
            $placement = $placements->firstWhere('id', $placementId);
            $room = $placement ? $rooms->firstWhere('id', (int) $placement->kamar_id) : null;

            if (!$placement || !$room || $placement->status !== 'scheduled' || $placement->check_in_at || $reservation->status !== 'approved') {
                throw ValidationException::withMessages(['placement' => 'Penempatan ini tidak dapat check-in.']);
            }

            if (!Pembayaran::query()->where('reservasi_id', $reservation->id)->where('status', 'paid')->exists()) {
                throw ValidationException::withMessages(['placement' => 'Check-in hanya dapat dicatat setelah pembayaran reservasi diverifikasi lunas oleh Admin Keuangan.']);
            }

            $timezone = (string) config('app.timezone', 'UTC');
            $now = CarbonImmutable::now($timezone);
            $actualDate = $now->toDateString();
            $currentStart = $placement->tanggal_mulai->toDateString();
            $plannedEnd = $placement->tanggal_akhir->toDateString();
            if ($actualDate >= $plannedEnd) {
                throw ValidationException::withMessages(['placement' => 'Tanggal periode reservasi sudah berakhir; placement tidak dapat check-in.']);
            }

            if ($actualDate < $currentStart) {
                if ($placement->transfer_dari_id !== null) {
                    throw ValidationException::withMessages(['placement' => 'Check-in lebih awal untuk transfer harus diproses melalui workflow transfer.']);
                }

                $months = $reservation->durasi_bulan;
                $actualStartDate = CarbonImmutable::createFromFormat('!Y-m-d', $actualDate, $timezone);
                if ($months !== null && $months >= 1 && $months <= 12) {
                    $newEnd = $actualStartDate->addMonthsNoOverflow((int) $months)->toDateString();
                } else {
                    $oldStartDate = CarbonImmutable::createFromFormat('!Y-m-d', $currentStart, $timezone);
                    $oldEndDate = CarbonImmutable::createFromFormat('!Y-m-d', $plannedEnd, $timezone);
                    $newEnd = $actualStartDate->addDays((int) $oldStartDate->diffInDays($oldEndDate))->toDateString();
                }

                foreach ($placements->where('user_id', $placement->user_id) as $other) {
                    if ((int) $other->id === (int) $placement->id || !in_array($other->status, ['scheduled', 'active', 'no_show'], true)) {
                        continue;
                    }
                    $extensionEnd = $extensions
                        ->where('penempatan_id', $other->id)
                        ->where('status', 'approved')
                        ->max(fn ($extension) => $extension->tanggal_akhir_diajukan->toDateString());
                    $otherEnd = max($this->effectiveEnd($other), $extensionEnd ?? '0000-00-00');
                    if ($other->tanggal_mulai->toDateString() < $newEnd && $otherEnd > $actualDate) {
                        throw ValidationException::withMessages(['placement' => 'Periode check-in lebih awal bertumpang tindih dengan placement lain milik penghuni.']);
                    }
                }

                $otherPlacements = $placements->reject(fn ($other) => (int) $other->id === (int) $placement->id);
                if (!$this->occupancy->canFit($room, $otherPlacements, $actualDate, $newEnd)) {
                    throw ValidationException::withMessages(['placement' => 'Kapasitas kamar tidak cukup untuk menggeser periode check-in ke tanggal hari ini.']);
                }

                $placement->tanggal_mulai = $actualDate;
                $placement->tanggal_akhir = $newEnd;
                $reservation->tanggal_mulai_disetujui = $actualDate;
                $reservation->tanggal_akhir_disetujui = $newEnd;
                $entry = sprintf(
                    'Check-in lebih awal oleh admin #%d pada %s; periode bergeser dari %s–%s menjadi %s–%s. Durasi dan nominal tagihan dipertahankan.',
                    $actorId,
                    $now->format('Y-m-d H:i:s T'),
                    $currentStart,
                    $plannedEnd,
                    $actualDate,
                    $newEnd,
                );
                $placement->catatan_lifecycle = trim(($placement->catatan_lifecycle ? $placement->catatan_lifecycle . "\n" : '') . $entry);
                $reservation->save();
            }

            $placement->status = 'active';
            $placement->check_in_at = $now;
            $placement->check_in_oleh = $actorId;
            $placement->save();

            return $placement;
        }, 3);
    }

    public function rescheduleCheckIn(
        int $placementId,
        string $newStart,
        string $expectedStart,
        int $actorId,
        string $reason,
    ): PenempatanPenghuni {
        $snapshot = PenempatanPenghuni::query()->findOrFail($placementId);

        return DB::transaction(function () use ($snapshot, $placementId, $newStart, $expectedStart, $actorId, $reason) {
            // Gather related parent IDs without locks, then acquire all locks in canonical order.
            $relatedReservationIds = PenempatanPenghuni::query()
                ->where(function ($query) use ($snapshot) {
                    $query->where('user_id', $snapshot->user_id)
                        ->orWhere('kamar_id', $snapshot->kamar_id);
                })
                ->pluck('reservasi_id')
                ->push((int) $snapshot->reservasi_id)
                ->merge(Reservasi::query()->where('user_id', $snapshot->user_id)->pluck('id'))
                ->all();

            $reservations = $this->locks->lockReservations($relatedReservationIds);
            $reservation = $reservations->firstWhere('id', (int) $snapshot->reservasi_id);
            if (!$reservation) {
                throw ValidationException::withMessages(['placement' => 'Reservasi penempatan tidak ditemukan.']);
            }

            $this->locks->lockUser((int) $snapshot->user_id);
            $rooms = $this->locks->lockAllRooms();
            $placements = $this->locks->lockPlacementsForRoomsAndUser($rooms->modelKeys(), (int) $snapshot->user_id);
            $extensions = $this->locks->lockExtensionsForPlacements($placements->modelKeys());
            $placement = $placements->firstWhere('id', $placementId);
            $room = $placement ? $rooms->firstWhere('id', (int) $placement->kamar_id) : null;

            if (!$placement || !$room || $reservation->status !== 'approved'
                || $placement->status !== 'scheduled' || $placement->check_in_at
                || $placement->transfer_dari_id !== null) {
                throw ValidationException::withMessages(['placement' => 'Jadwal hanya dapat diubah untuk placement awal yang masih menunggu check-in.']);
            }

            $currentStart = $placement->tanggal_mulai->toDateString();
            if ($currentStart !== $expectedStart) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Jadwal telah diubah oleh admin lain. Muat ulang halaman sebelum mencoba lagi.']);
            }
            if ($newStart === $currentStart) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Pilih tanggal yang berbeda dari jadwal saat ini.']);
            }

            $timezone = (string) config('app.timezone', 'UTC');
            $today = CarbonImmutable::now($timezone)->toDateString();
            if ($currentStart < $today || $newStart < $today) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Jadwal yang sudah terlewat harus ditangani melalui alur no-show, dan jadwal baru harus hari ini atau setelahnya.']);
            }

            $startDate = CarbonImmutable::createFromFormat('!Y-m-d', $newStart, $timezone);
            if (!$startDate || $startDate->format('Y-m-d') !== $newStart) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Tanggal check-in tidak valid.']);
            }

            $oldEnd = $placement->tanggal_akhir->toDateString();
            $months = $reservation->durasi_bulan;
            if ($months !== null && $months >= 1 && $months <= 12) {
                $newEnd = $startDate->addMonthsNoOverflow((int) $months)->toDateString();
            } else {
                $oldStartDate = CarbonImmutable::createFromFormat('!Y-m-d', $currentStart, $timezone);
                $oldEndDate = CarbonImmutable::createFromFormat('!Y-m-d', $oldEnd, $timezone);
                $durationDays = (int) $oldStartDate->diffInDays($oldEndDate);
                $newEnd = $startDate->addDays($durationDays)->toDateString();
            }

            if ($newStart >= $newEnd) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Tanggal akhir hasil penjadwalan harus setelah tanggal mulai.']);
            }

            foreach ($placements->where('user_id', $placement->user_id) as $other) {
                if ($other->id === $placement->id || !in_array($other->status, ['scheduled', 'active', 'no_show'], true)) {
                    continue;
                }
                $extensionEnd = $extensions
                    ->where('penempatan_id', $other->id)
                    ->where('status', 'approved')
                    ->max(fn ($extension) => $extension->tanggal_akhir_diajukan->toDateString());
                $otherEnd = max($this->effectiveEnd($other), $extensionEnd ?? '0000-00-00');
                if ($other->tanggal_mulai->toDateString() < $newEnd && $otherEnd > $newStart) {
                    throw ValidationException::withMessages(['tanggal_mulai' => 'Penghuni memiliki placement lain yang bertumpang tindih pada tanggal baru.']);
                }
            }

            $otherPlacements = $placements->reject(fn ($other) => (int) $other->id === (int) $placement->id);
            if (!$this->occupancy->canFit($room, $otherPlacements, $newStart, $newEnd)) {
                throw ValidationException::withMessages(['tanggal_mulai' => 'Kapasitas kamar tidak cukup untuk periode setelah jadwal check-in diubah.']);
            }

            $now = CarbonImmutable::now($timezone);
            $placement->tanggal_mulai = $newStart;
            $placement->tanggal_akhir = $newEnd;
            $entry = sprintf(
                'Jadwal check-in diubah oleh admin #%d pada %s dari %s menjadi %s. Alasan: %s',
                $actorId,
                $now->format('Y-m-d H:i:s T'),
                $currentStart,
                $newStart,
                trim($reason),
            );
            $placement->catatan_lifecycle = trim(($placement->catatan_lifecycle ? $placement->catatan_lifecycle . "\n" : '') . $entry);
            $placement->save();

            $reservation->tanggal_mulai_disetujui = $newStart;
            $reservation->tanggal_akhir_disetujui = $newEnd;
            $reservation->save();

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
            $extensions = $this->locks->lockExtensionsForPlacements($placements->modelKeys());
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

            foreach ($extensions->where('penempatan_id', $placement->id) as $extension) {
                if (in_array($extension->status, ['pending', 'approved'], true)) {
                    $extension->status = 'cancelled';
                    $extension->diputuskan_oleh = $actorId;
                    $extension->diputuskan_at = $checkoutAt;
                    $extension->catatan_admin = trim(($extension->catatan_admin ? $extension->catatan_admin . "\n" : '') . 'Dibatalkan karena penghuni checkout: ' . $reason);
                    $extension->save();
                }
            }

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
            $extensions = $this->locks->lockExtensionsForPlacements($placements->modelKeys());
            $source = $placements->firstWhere('id', $sourcePlacementId);
            $target = $rooms->firstWhere('id', $targetRoomId);

            if (!$source || $source->status !== 'active' || !$source->check_in_at || $source->check_out_at
                || $reservation->status !== 'approved') {
                throw ValidationException::withMessages(['placement' => 'Hanya penempatan aktif yang dapat dipindahkan.']);
            }
            if ($extensions->contains(fn ($extension) => (int) $extension->penempatan_id === $sourcePlacementId
                && in_array($extension->status, ['pending', 'approved'], true))) {
                throw ValidationException::withMessages(['placement' => 'Selesaikan atau batalkan pengajuan perpanjangan yang masih terbuka sebelum memindahkan kamar.']);
            }
            if (!$target || (int) $target->id === (int) $source->kamar_id
                || $target->kategori !== $user->jenis_kelamin
                || !$target->is_active || $target->status === 'perbaikan') {
                throw ValidationException::withMessages(['kamar_id' => 'Kamar tujuan tidak aktif atau tidak sesuai kategori penghuni.']);
            }
            $targetBranchId = (int) $target->cabang_asrama_id;
            $targetBranch = $this->locks->lockBranches([$targetBranchId])->firstWhere('id', $targetBranchId);
            if (!$targetBranch || !$targetBranch->is_active) {
                throw ValidationException::withMessages(['kamar_id' => 'Kamar tujuan harus berada pada cabang asrama yang aktif.']);
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
                'cabang_asrama_id' => $targetBranchId,
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
