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

    <!-- Grid Kartu Laporan (6 Kartu Sesuai Desain) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        <!-- Laporan Pendapatan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'pendapatan') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Pendapatan</h3>
                <p class="text-xs text-slate-500 mt-1">Rincian pendapatan sewa bulanan</p>
            </div>
        </div>

        <!-- Log Pembayaran -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'log-pembayaran') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Log Pembayaran</h3>
                <p class="text-xs text-slate-500 mt-1">Seluruh rekam jejak transaksi pembayaran</p>
            </div>
        </div>

        <!-- Laporan Tunggakan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'tunggakan') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Belum Lunas</h3>
                <p class="text-xs text-slate-500 mt-1">Daftar pembayaran tertunda & terlambat</p>
            </div>
        </div>

        <!-- Laporan Penolakan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'penolakan') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Penolakan</h3>
                <p class="text-xs text-slate-500 mt-1">Analisis bukti pembayaran yang ditolak</p>
            </div>
        </div>

        <!-- Rincian Metode Pembayaran -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'metode-pembayaran') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Rincian Metode Pembayaran</h3>
                <p class="text-xs text-slate-500 mt-1">Distribusi penggunaan kanal pembayaran</p>
            </div>
        </div>

        <!-- Ringkasan Tahunan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </div>
                    <a href="{{ route('keuangan.laporan.export', 'ringkasan-tahunan') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Ringkasan Tahunan</h3>
                <p class="text-xs text-slate-500 mt-1">Rekapitulasi keuangan tahunan secara menyeluruh</p>
            </div>
        </div>

    </div>

    <!-- Grafik Bulanan (Pendapatan vs Menunggu) -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900">Pendapatan vs Menunggu Verifikasi (12 Bulan)</h3>
        <div class="h-64 flex items-end justify-between border-b border-slate-200 pb-2 text-[11px] text-slate-400">
            <span>Jan</span>
            <span>Feb</span>
            <span>Mar</span>
            <span>Apr</span>
            <span>Mei</span>
            <span>Jun</span>
            <span>Jul</span>
            <span>Agu</span>
            <span>Sep</span>
            <span>Okt</span>
            <span>Nov</span>
            <span>Des</span>
        </div>
    </div>

</x-layouts.keuangan>