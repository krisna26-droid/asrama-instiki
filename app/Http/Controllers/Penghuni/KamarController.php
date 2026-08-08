<?php

namespace App\Http\Controllers\Penghuni;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::query();

        // Filter ketersediaan (default: hanya yang tersedia)
        if ($request->get('filter') === 'semua') {
            // Menampilkan semua kamar
        } else {
            // Default hanya menampilkan kamar berstatus tersedia
            $query->where('status', 'tersedia');
        }

        // Pencarian berdasarkan nomor kamar atau blok
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nomor_kamar', 'like', "%{$search}%")
                  ->orWhere('blok', 'like', "%{$search}%")
                  ->orWhere('fasilitas', 'like', "%{$search}%");
            });
        }

        $kamars = $query->latest()->get();

        return view('penghuni.kamar.index', compact('kamars'));
    }
}