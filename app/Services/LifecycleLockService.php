<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\PerpanjanganPenempatan;
use App\Models\PenempatanPenghuni;
use App\Models\Reservasi;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Lock helper for lifecycle writes. Callers must already be inside a transaction.
 * Canonical order: reservations, users, rooms, placements, extensions.
 */
class LifecycleLockService
{
    /** @param array<int, int> $reservationIds */
    public function lockReservations(array $reservationIds): Collection
    {
        $ids = collect($reservationIds)->map(fn ($id) => (int) $id)->unique()->sort()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return Reservasi::query()
            ->whereIn('id', $ids)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    public function lockReservation(int $reservationId): Reservasi
    {
        return Reservasi::query()->whereKey($reservationId)->lockForUpdate()->firstOrFail();
    }

    public function lockUser(int $userId): User
    {
        return User::query()->whereKey($userId)->lockForUpdate()->firstOrFail();
    }

    public function lockAllRooms(): Collection
    {
        return Kamar::query()->orderBy('id')->lockForUpdate()->get();
    }

    public function lockPlacementsForRoomsAndUser(array $roomIds, int $userId): Collection
    {
        return PenempatanPenghuni::query()
            ->where(function ($query) use ($roomIds, $userId) {
                if ($roomIds !== []) {
                    $query->whereIn('kamar_id', $roomIds)->orWhere('user_id', $userId);
                } else {
                    $query->where('user_id', $userId);
                }
            })
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }

    public function lockExtensionsForPlacements(array $placementIds): Collection
    {
        if ($placementIds === []) {
            return collect();
        }

        return PerpanjanganPenempatan::query()
            ->whereIn('penempatan_id', $placementIds)
            ->orderBy('id')
            ->lockForUpdate()
            ->get();
    }
}
