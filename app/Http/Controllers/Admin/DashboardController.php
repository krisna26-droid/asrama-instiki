<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use App\Models\Reservasi;
use App\Models\PenempatanPenghuni;
use App\Services\RoomOccupancyService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(RoomOccupancyService $occupancy)
    {
        $rooms = Kamar::query()->where('is_active', true)->whereHas('cabangAsrama', fn ($branch) => $branch->where('is_active', true))->withCount(['penempatans as penghuni_aktif_count' => fn ($query) => $query->where('status', 'active')->whereNotNull('check_in_at')->whereNull('check_out_at')->whereDate('tanggal_mulai', '<=', now()->toDateString())->whereDate('tanggal_akhir', '>', now()->toDateString())])->get();
        $totalKamar = $rooms->count();
        $kamarTersedia = $rooms->filter(fn ($room) => $room->status !== 'perbaikan' && $room->penghuni_aktif_count < $room->kapasitas)->count();
        $kamarPenuh = $rooms->filter(fn ($room) => $room->status !== 'perbaikan' && $room->penghuni_aktif_count >= $room->kapasitas)->count();
        $kamarPerbaikan = $rooms->where('status', 'perbaikan')->count();
        $capacitySummary = $occupancy->currentCapacitySummary();
        $branchCapacitySummaries = $occupancy->capacitySummaryByBranch();
        $pendingReservasi = Reservasi::where('status', 'pending')->count();
        $latestReservasi = Reservasi::with(['user', 'kamar.cabangAsrama', 'cabangAsrama', 'penempatans.kamar.cabangAsrama', 'penempatans.cabangAsrama'])->latest()->limit(5)->get();

        return view('admin.dashboard', compact(
            'totalKamar',
            'kamarTersedia',
            'kamarPenuh',
            'kamarPerbaikan',
            'pendingReservasi',
            'latestReservasi'
            , 'capacitySummary', 'branchCapacitySummaries'
        ));
    }
}
