<x-layouts.keuangan 
    title="Riwayat Pembayaran - Admin Keuangan INSTIKI" 
    activeMenu="riwayat"
    searchRoute="{{ route('keuangan.riwayat.index') }}"
    searchPlaceholder="Cari nama penghuni atau kode pembayaran...">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Riwayat Pembayaran</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Arsip seluruh transaksi pembayaran yang telah diproses</p>
    </div>

    <!-- Kontainer Tabel Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden p-4 sm:p-6 space-y-4">
        
        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-slate-400 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Penghuni</th>
                        <th class="py-3.5 px-4">Jumlah</th>
                        <th class="py-3.5 px-4">Periode</th>
                        <th class="py-3.5 px-4">Metode</th>
                        <th class="py-3.5 px-4">Tanggal</th>
                        <th class="py-3.5 px-4">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayats as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500 font-medium">{{ $item->kode_pembayaran }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->user->nama ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ \Carbon\Carbon::parse($item->created_at)->format('F Y') }}</td>
                            <td class="py-3.5 px-4 text-slate-600">{{ $item->metode_pembayaran }}</td>
                            <td class="py-3.5 px-4 text-slate-500 font-mono text-xs">{{ \Carbon\Carbon::parse($item->updated_at)->format('Y-m-d') }}</td>
                            <td class="py-3.5 px-4 flex items-center gap-2">
                                @if($item->status === 'paid')
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Lunas
                                    </span>
                                    <a href="{{ route('keuangan.pembayaran.kuitansi', $item->id) }}" target="_blank" 
                                       class="px-2 py-1 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition inline-flex items-center gap-1 shadow-sm">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Cetak Kuitansi
                                    </a>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-rose-100 text-rose-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">Belum ada riwayat transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.keuangan>