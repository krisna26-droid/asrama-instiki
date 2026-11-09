<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Services\RoomOccupancyService;

class KamarController extends Controller
{
    public function index(RoomOccupancyService $occupancy)
    {
        $capacity = $occupancy->currentCapacitySummary();
        $hasActiveRequest = auth()->user()->punyaKamarAktif();
        return view('penghuni.kamar.index', compact('capacity', 'hasActiveRequest'));
    }
}
