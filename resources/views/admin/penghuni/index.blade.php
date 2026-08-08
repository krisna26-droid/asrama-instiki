<x-layouts.admin 
    title="Penghuni Aktif - Admin Asrama INSTIKI" 
    activeMenu="penghuni"
    searchRoute="{{ route('admin.penghuni.index') }}"
    searchPlaceholder="Cari penghuni, NIM, atau nomor kamar...">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Penghuni Aktif</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Daftar seluruh mahasiswa yang saat ini aktif menempati kamar asrama.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Table Content -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase text-[11px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Penghuni</th>
                        <th class="py-3.5 px-4">Program Studi</th>
                        <th class="py-3.5 px-4">Kamar</th>
                        <th class="py-3.5 px-4">Tanggal Masuk</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penghunis as $item)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 font-bold flex items-center justify-center text-xs border border-slate-200 shrink-0">
                                        {{ strtoupper(substr($item->user->nama ?? 'U', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 block">{{ $item->user->nama ?? '-' }}</span>
                                        <span class="text-[11px] text-slate-400 block font-mono">{{ $item->user->nim_nik ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                Informatika
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->kamar)
                                    <span class="font-bold text-slate-800 block">Kamar {{ $item->kamar->nomor_kamar }}</span>
                                    <span class="text-[11px] text-slate-400 block">Blok {{ $item->kamar->blok }}</span>
                                @else
                                    <span class="text-amber-600 italic font-medium">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 font-medium">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-800 rounded-full inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Aktif
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <a href="{{ route('admin.penghuni.show', $item->id) }}" class="p-1.5 text-slate-400 hover:text-slate-700 transition inline-block" title="Lihat Detail">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">Belum ada data penghuni aktif.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.admin>