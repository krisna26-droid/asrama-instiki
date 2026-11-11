<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CabangAsrama;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class CabangAsramaController extends Controller
{
    public function index()
    {
        $cabangs = CabangAsrama::query()
            ->with(['kamars' => fn ($query) => $query->orderBy('nomor_kamar')])
            ->orderBy('nama')
            ->paginate(12);

        return view('admin.cabang.index', compact('cabangs'));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        CabangAsrama::query()->create($data + ['is_active' => true]);

        return back()->with('success', 'Cabang asrama berhasil ditambahkan.');
    }

    public function update(Request $request, CabangAsrama $cabang)
    {
        $data = $this->validated($request, $cabang);
        $cabang->update($data);

        return back()->with('success', 'Informasi cabang asrama diperbarui.');
    }

    public function toggle(CabangAsrama $cabang)
    {
        DB::transaction(function () use ($cabang): void {
            // Match allocation order: room rows first, branch row after them.
            Kamar::query()->orderBy('id')->lockForUpdate()->get(['id']);
            $locked = CabangAsrama::query()->whereKey($cabang->id)->lockForUpdate()->firstOrFail();
            if ($locked->is_active) {
                $hasUnresolvedPlacement = \App\Models\PenempatanPenghuni::query()
                    ->whereIn('status', ['scheduled', 'active', 'no_show'])
                    ->where(function ($query) use ($locked): void {
                        $query->where('cabang_asrama_id', $locked->id)
                            ->orWhereHas('kamar', fn ($room) => $room->where('cabang_asrama_id', $locked->id));
                    })
                    ->exists();
                if ($hasUnresolvedPlacement) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'cabang' => 'Cabang tidak dapat dinonaktifkan selama memiliki placement aktif, terjadwal, atau no-show yang belum diselesaikan.',
                    ]);
                }
            }
            $locked->is_active = !$locked->is_active;
            $locked->save();
        }, 3);

        return back()->with('success', 'Status cabang diperbarui. Histori dan placement yang sudah tercatat tetap dipertahankan.');
    }

    private function validated(Request $request, ?CabangAsrama $cabang = null): array
    {
        return $request->validate([
            'kode' => ['required', 'string', 'max:30', Rule::unique('cabang_asramas', 'kode')->ignore($cabang?->id)],
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['required', 'string', 'max:5000'],
            'maps_url' => ['nullable', 'url', 'max:2048'],
            'telepon' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
        ]);
    }
}
