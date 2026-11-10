<?php

namespace App\Http\Controllers;

use App\Models\AsramaSetting;
use App\Models\FasilitasAsrama;
use App\Services\RoomOccupancyService;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PublicAsramaController extends Controller
{
    public function index(RoomOccupancyService $occupancy): View
    {
        $facilities = $this->hasColumns('fasilitas_asramas', ['id', 'judul', 'slug', 'caption', 'gambar_path', 'urutan', 'aktif'])
            ? FasilitasAsrama::query()->where('aktif', true)->orderBy('urutan')->orderBy('id')->get()
            : collect();

        $contact = $this->hasColumns('asrama_settings', ['id', 'whatsapp', 'telepon', 'email', 'alamat'])
            ? AsramaSetting::query()->find(1)
            : null;

        return view('welcome', array_merge(
            $occupancy->currentCapacitySummary(),
            ['facilities' => $facilities, 'contact' => $contact],
        ));
    }

    public function showDetails(): View
    {
        $facilities = $this->hasColumns('fasilitas_asramas', ['id', 'aktif', 'judul', 'caption', 'deskripsi', 'gambar_path', 'urutan'])
            ? FasilitasAsrama::query()->where('aktif', true)->orderBy('urutan')->orderBy('id')->get()
            : collect();

        $contact = $this->hasColumns('asrama_settings', ['id', 'whatsapp', 'telepon', 'email', 'alamat'])
            ? AsramaSetting::query()->find(1)
            : null;

        return view('asrama.detail', compact('facilities', 'contact'));
    }

    private function hasColumns(string $table, array $columns): bool
    {
        if (! Schema::hasTable($table)) {
            return false;
        }

        foreach ($columns as $column) {
            if (! Schema::hasColumn($table, $column)) {
                return false;
            }
        }

        return true;
    }
}
