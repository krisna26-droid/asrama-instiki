<?php

namespace App\Console\Commands;

use App\Models\PenempatanPenghuni;
use App\Services\ExtensionLifecycleService;
use Illuminate\Console\Command;

class CompleteEndedPlacements extends Command
{
    protected $signature = 'asrama:complete-ended-placements';

    protected $description = 'Selesaikan placement aktif yang telah mencapai tanggal akhir tanpa perpanjangan lunas.';

    public function handle(ExtensionLifecycleService $lifecycle): int
    {
        $today = now()->toDateString();
        $placementIds = PenempatanPenghuni::query()
            ->where('status', 'active')
            ->whereNotNull('check_in_at')
            ->whereNull('check_out_at')
            ->whereDate('tanggal_akhir', '<=', $today)
            ->orderBy('id')
            ->pluck('id');

        $completed = 0;
        foreach ($placementIds as $placementId) {
            if ($lifecycle->finalizeEndedPlacement((int) $placementId)) {
                $completed++;
            }
        }

        $this->info("Placement diselesaikan: {$completed}.");
        return self::SUCCESS;
    }
}
