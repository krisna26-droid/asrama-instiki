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
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'okupansi') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Okupansi Kamar</h3>
                <p class="text-xs text-slate-500 mt-1">Tingkat keterisian dan ketersediaan kamar asrama.</p>
            </div>
        </div>

        <!-- Laporan Data Penghuni -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'penghuni') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Data Penghuni</h3>
                <p class="text-xs text-slate-500 mt-1">Direktori lengkap data mahasiswa penghuni aktif.</p>
            </div>
        </div>

        <!-- Laporan Reservasi -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'reservasi') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Pengajuan Reservasi</h3>
                <p class="text-xs text-slate-500 mt-1">Rekapitulasi persetujuan dan penolakan pendaftaran.</p>
            </div>
        </div>

        <!-- Laporan Pendapatan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'pendapatan') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Laporan Pendapatan Sewa</h3>
                <p class="text-xs text-slate-500 mt-1">Rekap bulanan pembayaran biaya sewa kamar.</p>
            </div>
        </div>

        <!-- Laporan Pemeliharaan Kamar -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'pemeliharaan') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Catatan Pemeliharaan</h3>
                <p class="text-xs text-slate-500 mt-1">Riwayat perbaikan dan kondisi fisik fasilitas kamar.</p>
            </div>
        </div>

        <!-- Laporan Penggunaan Fasilitas -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between hover:border-slate-300 transition">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5"></path></svg>
                    </div>
                    <a href="{{ route('admin.laporan.export', 'fasilitas') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Ekspor</a>
                </div>
                <h3 class="text-sm font-bold text-slate-900">Penggunaan Fasilitas</h3>
                <p class="text-xs text-slate-500 mt-1">Rincian utilitas dan kelengkapan kamar asrama.</p>
            </div>
        </div>

    </div>

    <!-- Section Grafik / Statistik Bulanan -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="text-sm font-bold text-slate-900">Okupansi vs Reservasi Bulanan</h3>
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

</x-layouts.admin>