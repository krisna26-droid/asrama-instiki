<?php

namespace App\Services;

use App\Models\Kamar;
use App\Models\PenempatanPenghuni;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class RoomOccupancyService
{
    /** Capacity is checked on half-open calendar intervals [start, end). */
    public function canFit(Kamar $room, Collection $placements, string $start, string $end): bool
    {
        if (!$room->is_active || $room->status === 'perbaikan' || $room->kapasitas < 1 || $start >= $end) {
            return false;
        }

        $events = [[$start, 1], [$end, -1]];
        foreach ($placements as $placement) {
            if ((int) $placement->kamar_id !== (int) $room->id || !in_array($placement->status, ['scheduled', 'active', 'no_show'], true)) {
                continue;
            }

            $existingStart = $placement->tanggal_mulai->toDateString();
            $existingEnd = $this->effectiveEnd($placement);
            if ($existingStart >= $end || $existingEnd <= $start) {
                continue;
            }

            $events[] = [max($existingStart, $start), 1];
            $events[] = [min($existingEnd, $end), -1];
        }

        usort($events, static fn (array $a, array $b) => ($a[0] <=> $b[0]) ?: ($a[1] <=> $b[1]));
        $current = 0;
        foreach ($events as [, $delta]) {
            $current += $delta;
            if ($current > $room->kapasitas) {
                return false;
            }
        }

        return true;
    }

    public function peakConcurrentOccupancy(Collection $placements, ?string $fromDate = null): int
    {
        $fromDate ??= now()->toDateString();
        $events = [];
        foreach ($placements as $placement) {
            if (!in_array($placement->status, ['scheduled', 'active', 'no_show'], true)) {
                continue;
            }
            $start = max($placement->tanggal_mulai->toDateString(), $fromDate);
            $end = $this->effectiveEnd($placement);
            if ($end <= $start) {
                continue;
            }
            $events[] = [$start, 1];
            $events[] = [$end, -1];
        }

        usort($events, static fn (array $a, array $b) => ($a[0] <=> $b[0]) ?: ($a[1] <=> $b[1]));
        $current = $peak = 0;
        foreach ($events as [, $delta]) {
            $current += $delta;
            $peak = max($peak, $current);
        }
        return $peak;
    }

    public function currentCapacitySummary(): array
    {
        return $this->capacitySummaryAt(now()->toDateString());
    }

    public function capacitySummaryAt(string $date): array
    {
        if (!Schema::hasTable('kamars')) {
            return $this->unavailableSummary();
        }

        $roomQuery = Kamar::query()->where('status', '!=', 'perbaikan');
        if (Schema::hasColumn('kamars', 'is_active')) {
            $roomQuery->where('is_active', true);
        }
        $rooms = $roomQuery->get(['id', 'kapasitas']);
        $capacity = (int) $rooms->sum('kapasitas');
        $roomIds = $rooms->modelKeys();

        $placementColumns = ['kamar_id', 'status', 'tanggal_mulai', 'tanggal_akhir', 'check_in_at', 'check_out_at', 'user_id'];
        if (!Schema::hasTable('penempatan_penghunis')) {
            return $this->unavailableSummary($capacity);
        }
        foreach ($placementColumns as $column) {
            if (!Schema::hasColumn('penempatan_penghunis', $column)) {
                return $this->unavailableSummary($capacity);
            }
        }

        $candidatePlacements = PenempatanPenghuni::query()
            ->whereIn('kamar_id', $roomIds)
            ->whereIn('status', ['active', 'completed'])
            ->whereNotNull('check_in_at')
            ->whereDate('tanggal_mulai', '<=', $date)
            ->get();
        $occupied = $candidatePlacements
            ->filter(fn ($placement) => $placement->status !== 'active' || !$placement->check_out_at)
            ->filter(fn ($placement) => $placement->tanggal_akhir->toDateString() > $date
                && (!$placement->check_out_at || $placement->check_out_at->toDateString() > $date))
            ->pluck('user_id')->unique()->count();

        $commitments = PenempatanPenghuni::query()
            ->whereIn('kamar_id', $roomIds)
            ->whereIn('status', ['scheduled', 'active', 'no_show'])
            ->whereDate('tanggal_mulai', '<=', $date)
            ->get();
        $committedBeds = $commitments->filter(function ($placement) use ($date) {
            if ($placement->status === 'no_show') return true;
            if ($placement->tanggal_akhir->toDateString() <= $date) return false;
            return !$placement->check_out_at || $placement->check_out_at->toDateString() > $date;
        })->count();

        $available = max(0, $capacity - $committedBeds);

        return [
            'schemaReady' => true,
            'totalKapasitas' => $capacity,
            'penghuniAktif' => $occupied,
            'kapasitasTerkomitmen' => $committedBeds,
            'kapasitasTersedia' => $available,
            'persentase' => $capacity > 0 ? round(($occupied / $capacity) * 100) : 0,
            'statusKetersediaan' => $available > 0 ? 'Tersedia' : 'Penuh',
        ];
    }

    private function unavailableSummary(int $capacity = 0): array
    {
        return [
            'schemaReady' => false,
            'totalKapasitas' => $capacity,
            'penghuniAktif' => null,
            'kapasitasTerkomitmen' => null,
            'kapasitasTersedia' => null,
            'persentase' => null,
            'statusKetersediaan' => 'Data kapasitas belum tersedia',
        ];
    }

    private function effectiveEnd(PenempatanPenghuni $placement): string
    {
        if ($placement->status === 'no_show') {
            return '9999-12-31';
        }
        $plannedEnd = $placement->tanggal_akhir->toDateString();
        if (!$placement->check_out_at) {
            return $plannedEnd;
        }

        $actualEnd = CarbonImmutable::instance($placement->check_out_at)->toDateString();
        return min($plannedEnd, $actualEnd);
    }
}
