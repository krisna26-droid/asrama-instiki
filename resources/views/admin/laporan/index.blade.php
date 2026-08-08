<x-layouts.admin 
    title="Laporan Asrama - Admin Asrama INSTIKI" 
    activeMenu="laporan"
    searchPlaceholder="Cari laporan...">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Laporan Asrama</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Cetak dan ekspor rekapitulasi data operasional asrama.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Grid Kartu Laporan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Laporan Okupansi Kamar -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'okupansi') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Okupansi Kamar</h3>
                <p class="text-xs text-slate-500 mt-1">Tingkat keterisian dan ketersediaan kamar asrama.</p>
            </div>
        </div>

        <!-- Laporan Data Penghuni -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'penghuni') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Data Penghuni</h3>
                <p class="text-xs text-slate-500 mt-1">Direktori lengkap data mahasiswa penghuni aktif.</p>
            </div>
        </div>

        <!-- Laporan Pengajuan Reservasi -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'reservasi') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Pengajuan Reservasi</h3>
                <p class="text-xs text-slate-500 mt-1">Rekapitulasi persetujuan dan penolakan pendaftaran.</p>
            </div>
        </div>

        <!-- Laporan Pendapatan Sewa -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'pendapatan') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Pendapatan Sewa</h3>
                <p class="text-xs text-slate-500 mt-1">Rekap bulanan pembayaran biaya sewa kamar.</p>
            </div>
        </div>

        <!-- Catatan Pemeliharaan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'pemeliharaan') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Catatan Pemeliharaan</h3>
                <p class="text-xs text-slate-500 mt-1">Riwayat perbaikan dan kondisi fisik fasilitas kamar.</p>
            </div>
        </div>

        <!-- Penggunaan Fasilitas -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-teal-50 flex items-center justify-center text-teal-600 border border-teal-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'fasilitas') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Penggunaan Fasilitas</h3>
                <p class="text-xs text-slate-500 mt-1">Rincian utilitas dan kelengkapan kamar asrama.</p>
            </div>
        </div>

    </div>

    <!-- Grafik Bulanan Dinamis (Total Reservasi vs Disetujui) -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Total Pengajuan vs Disetujui (Tahun {{ date('Y') }})</h3>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 text-slate-600">
                    <span class="w-3 h-3 rounded bg-blue-500"></span>
                    Total Pengajuan
                </span>
                <span class="inline-flex items-center gap-1.5 text-slate-600">
                    <span class="w-3 h-3 rounded bg-emerald-500"></span>
                    Pengajuan Disetujui
                </span>
            </div>
        </div>

        <div class="h-64 flex items-end justify-between gap-2 pt-8 px-2 border-b border-slate-100">
            @php
                $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            @endphp

            @foreach($namaBulan as $bulanIdx => $label)
                @php
                    $valTotal    = $chartTotal[$bulanIdx] ?? 0;
                    $valApproved = $chartApproved[$bulanIdx] ?? 0;

                    // Menggunakan $maxCount yang dikirim dari LaporanController
                    // dengan validasi nilai default 1 jika pembagi 0 (misal basis data kosong)
                    $divisor     = (!empty($maxCount) && $maxCount > 0) ? $maxCount : 1;

                    $hTotal      = $valTotal > 0 ? round(($valTotal / $divisor) * 100) : 3;
                    $hApproved   = $valApproved > 0 ? round(($valApproved / $divisor) * 100) : 3;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end group relative">
                    <!-- Tooltip Hover -->
                    <div class="opacity-0 group-hover:opacity-100 transition absolute -top-10 bg-slate-800 text-white text-[10px] py-1 px-2 rounded font-mono pointer-events-none z-20 whitespace-nowrap shadow-md">
                        Total: {{ $valTotal }} | Disetujui: {{ $valApproved }}
                    </div>

                    <!-- Batang Grafik Ganda -->
                    <div class="w-full flex items-end justify-center gap-1 h-full">
                        <div class="w-1/2 rounded-t transition-all duration-300 {{ $valTotal > 0 ? 'bg-blue-500 hover:bg-blue-600' : 'bg-slate-100' }}"
                             style="height: {{ $hTotal }}%;">
                        </div>
                        <div class="w-1/2 rounded-t transition-all duration-300 {{ $valApproved > 0 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-100' }}"
                             style="height: {{ $hApproved }}%;">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-1 px-2 font-mono">
            @foreach($namaBulan as $m)
                <span class="flex-1 text-center">{{ $m }}</span>
            @endforeach
        </div>
    </div>

</x-layouts.admin>