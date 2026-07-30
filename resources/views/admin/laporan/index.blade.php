<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Asrama - Admin Asrama INSTIKI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
    <div class="min-h-screen flex">

        <!-- Sidebar Kiri -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 hidden md:flex">
            <div>
                <div class="h-20 flex items-center px-6 border-b border-slate-100">
                    <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-9 w-auto">
                    <div class="ml-3 border-l border-slate-200 pl-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Asrama</span>
                        <span class="text-[10px] text-slate-500">INSTIKI</span>
                    </div>
                </div>

                <div class="px-4 py-6">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-3">Menu Utama</span>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('kamar.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Manajemen Kamar
                        </a>
                        <a href="{{ route('admin.reservasi.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verifikasi Reservasi
                        </a>
                        <a href="{{ route('admin.penghuni.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Penghuni Aktif
                        </a>
                        <a href="{{ route('admin.laporan.index') }}" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-lg bg-red-50 text-[#ed1c24]">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Laporan Asrama
                        </a>
                    </nav>
                </div>
            </div>

            <div class="p-4 border-t border-slate-200">
                <div class="bg-slate-50 p-3 rounded-lg">
                    <p class="text-xs font-bold text-slate-800">Admin Asrama</p>
                    <p class="text-[10px] text-slate-500">v1.0 Operational</p>
                </div>
            </div>
        </aside>

        <!-- Area Utama -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Navbar -->
            <header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-10">
                <div class="w-72 sm:w-96">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" placeholder="Cari laporan..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3 text-right">
                        <div>
                            <span class="block text-sm font-bold text-slate-800">{{ auth()->user()->nama }}</span>
                            <span class="block text-xs text-slate-500">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-red-100 text-[#ed1c24] font-bold flex items-center justify-center text-sm border border-red-200">
                            {{ substr(auth()->user()->nama, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-6 sm:p-8 space-y-6 flex-1 overflow-y-auto">

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

            </main>
        </div>

    </div>
</body>
</html>