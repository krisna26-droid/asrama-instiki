<x-layouts.keuangan 
    title="Laporan Keuangan - Admin Keuangan INSTIKI" 
    activeMenu="laporan"
    searchPlaceholder="Cari laporan keuangan...">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Laporan Keuangan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Laporan operasional keuangan dan ekspor rekapitulasi pembayaran</p>
    </div>

    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Grid Kartu Laporan (6 Kartu Ekspor) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- 1. Laporan Pendapatan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'pendapatan') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Pendapatan</h3>
                <p class="text-xs text-slate-500 mt-1">Rincian pendapatan sewa bulanan terverifikasi lunas</p>
            </div>
        </div>

        <!-- 2. Log Pembayaran -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'log-pembayaran') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Log Pembayaran</h3>
                <p class="text-xs text-slate-500 mt-1">Seluruh rekam jejak transaksi pembayaran pengguna</p>
            </div>
        </div>

        <!-- 3. Laporan Belum Lunas -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600 border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'tunggakan') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Menunggu Verifikasi</h3>
                <p class="text-xs text-slate-500 mt-1">Daftar transaksi pembayaran yang perlu diverifikasi</p>
            </div>
        </div>

        <!-- 4. Laporan Penolakan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-rose-50 flex items-center justify-center text-rose-600 border border-rose-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'penolakan') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Penolakan</h3>
                <p class="text-xs text-slate-500 mt-1">Analisis dan catatan bukti pembayaran yang ditolak</p>
            </div>
        </div>

        <!-- 5. Rincian Metode Pembayaran -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'metode-pembayaran') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Rincian Metode Pembayaran</h3>
                <p class="text-xs text-slate-500 mt-1">Distribusi penggunaan kanal pembayaran</p>
            </div>
        </div>

        <!-- 6. Ringkasan Tahunan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 border border-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'ringkasan-tahunan') }}" class="px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-lg text-xs font-bold transition inline-flex items-center gap-1">
                        Ekspor CSV
                    </a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Ringkasan Tahunan</h3>
                <p class="text-xs text-slate-500 mt-1">Rekapitulasi keuangan tahunan secara menyeluruh</p>
            </div>
        </div>

    </div>

    <!-- Grafik Bulanan Dinamis (Pendapatan vs Menunggu Verifikasi) -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 pb-3">
            <h3 class="text-sm font-bold text-slate-900">Pendapatan Lunas vs Menunggu Verifikasi (Tahun {{ date('Y') }})</h3>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 text-slate-600">
                    <span class="w-3 h-3 rounded bg-emerald-500"></span>
                    Pendapatan Lunas
                </span>
                <span class="inline-flex items-center gap-1.5 text-slate-600">
                    <span class="w-3 h-3 rounded bg-amber-400"></span>
                    Menunggu Verifikasi
                </span>
            </div>
        </div>

        <div class="h-64 flex items-end justify-between gap-2 pt-8 px-2 border-b border-slate-100">
            @php
                $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
            @endphp

            @foreach($namaBulan as $bulanIdx => $label)
                @php
                    $valPaid    = $chartPaid[$bulanIdx] ?? 0;
                    $valPending = $chartPending[$bulanIdx] ?? 0;

                    $hPaid    = $valPaid > 0 ? round(($valPaid / $maxAmount) * 100) : 3;
                    $hPending = $valPending > 0 ? round(($valPending / $maxAmount) * 100) : 3;
                @endphp
                <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end group relative">
                    <!-- Tooltip Hover -->
                    <div class="opacity-0 group-hover:opacity-100 transition absolute -top-10 bg-slate-800 text-white text-[10px] py-1 px-2 rounded font-mono pointer-events-none z-20 whitespace-nowrap shadow-md">
                        Lunas: Rp {{ number_format($valPaid, 0, ',', '.') }} | Pending: Rp {{ number_format($valPending, 0, ',', '.') }}
                    </div>

                    <!-- Batang Grafik Ganda (Paid vs Pending) -->
                    <div class="w-full flex items-end justify-center gap-1 h-full">
                        <div class="w-1/2 rounded-t transition-all duration-300 {{ $valPaid > 0 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-100' }}"
                             style="height: {{ $hPaid }}%;">
                        </div>
                        <div class="w-1/2 rounded-t transition-all duration-300 {{ $valPending > 0 ? 'bg-amber-400 hover:bg-amber-500' : 'bg-slate-100' }}"
                             style="height: {{ $hPending }}%;">
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

</x-layouts.keuangan>