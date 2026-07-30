<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::query();

        // Filter Status jika tombol filter diklik
        if ($request->has('status') && $request->status != 'semua') {
            $query->where('status', $request->status);
        }

        // Pencarian Nama/Nomor Kamar
        if ($request->has('search') && $request->search != '') {
            $query->where('nomor_kamar', 'like', '%' . $request->search . '%')
                  ->orWhere('blok', 'like', '%' . $request->search . '%');
        }

        $kamars = $query->latest()->get();

        return view('admin.kamar.index', compact('kamars'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_kamar'   => 'required|unique:kamars,nomor_kamar',
            'blok'          => 'required|string',
            'lantai'        => 'required|numeric',
            'kategori'      => 'required|in:putra,putri',
            'kapasitas'     => 'required|numeric|min:1',
            'harga_bulanan' => 'required|numeric',
            'status'        => 'required|in:tersedia,tersewa_penuh,perbaikan',
            'fasilitas'     => 'nullable|string',
            'foto'          => 'nullable|string',
        ]);

        Kamar::create([
            'nomor_kamar'   => $request->nomor_kamar,
            'blok'          => $request->blok,
            'lantai'        => $request->lantai,
            'kategori'      => $request->kategori,
            'kapasitas'     => $request->kapasitas,
            'terisi'        => 0,
            'harga_bulanan' => $request->harga_bulanan,
            'status'        => $request->status,
            'fasilitas'     => $request->fasilitas,
            'foto'          => $request->foto ?? 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?q=80&w=800',
        ]);

        return redirect()->back()->with('success', 'Kamar baru berhasil ditambahkan.');
    }

    public function update(Request $request, Kamar $kamar)
    {
        $request->validate([
            'nomor_kamar'   => 'required|unique:kamars,nomor_kamar,' . $kamar->id,
            'blok'          => 'required|string',
            'lantai'        => 'required|numeric',
            'kategori'      => 'required|in:putra,putri',
            'kapasitas'     => 'required|numeric|min:1',
            'harga_bulanan' => 'required|numeric',
            'status'        => 'required|in:tersedia,tersewa_penuh,perbaikan',
            'fasilitas'     => 'nullable|string',
            'foto'          => 'nullable|string',
        ]);

        $kamar->update($request->all());

        return redirect()->back()->with('success', 'Data kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        $kamar->delete();
        return redirect()->back()->with('success', 'Kamar berhasil dihapus.');
    }
}