<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin Asrama INSTIKI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased">
    <div class="min-h-screen flex">

        <!-- Sidebar Kiri -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 hidden md:flex">
            <div>
                <!-- Brand Header -->
                <div class="h-20 flex items-center px-6 border-b border-slate-100">
                    <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-9 w-auto">
                    <div class="ml-3 border-l border-slate-200 pl-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Asrama</span>
                        <span class="text-[10px] text-slate-500">INSTIKI</span>
                    </div>
                </div>

                <!-- Navigasi Menu -->
                <div class="px-4 py-6">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-3">Menu Utama</span>
                    <nav class="space-y-1">
                        <!-- Dashboard -->
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-lg bg-red-50 text-[#ed1c24]">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>

                        <!-- Manajemen Kamar -->
                        <a href="{{ route('kamar.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Manajemen Kamar
                        </a>

                        <!-- Verifikasi Reservasi (Pastikan route terpasang di sini) -->
                        <a href="{{ route('admin.reservasi.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verifikasi Reservasi
                        </a>

                        <!-- Penghuni Aktif -->
                        <a href="{{ route('admin.penghuni.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Penghuni Aktif
                        </a>

                        <!-- Laporan -->
                        <a href="{{ route('admin.laporan.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Laporan
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Profile Info Footer Bottom -->
            <div class="p-4 border-t border-slate-200">
                <div class="bg-slate-50 p-3 rounded-lg flex items-center justify-between">
                    <div>
                        <p class="text-xs font-bold text-slate-800">Admin Asrama</p>
                        <p class="text-[10px] text-slate-500">v1.0 Operational</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Navbar -->
            <header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-10">
                <!-- Search Bar -->
                <div class="w-72 sm:w-96">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" placeholder="Cari kamar, penghuni, atau reservasi..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
                    </div>
                </div>

                <!-- User Profile & Action -->
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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Out" class="p-2 text-slate-500 hover:text-[#ed1c24] transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Body Dashboard -->
            <main class="p-6 sm:p-8 space-y-8 flex-1 overflow-y-auto">

                <!-- Heading -->
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Ringkasan Operasional</h1>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1">Gambaran umum ketersediaan kamar, reservasi, dan aktivitas asrama INSTIKI.</p>
                </div>

                <!-- Grid Status Cards (4 Cards) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Total Kamar -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200 flex justify-between items-start shadow-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Total Kamar</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $totalKamar }}</h3>
                            <span class="text-[11px] font-medium text-slate-400 mt-2 block">Kapasitas 3 org/kamar</span>
                        </div>
                        <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 5h5"></path></svg>
                        </div>
                    </div>

                    <!-- Kamar Tersedia -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200 flex justify-between items-start shadow-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Kamar Tersedia</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kamarTersedia }}</h3>
                            <span class="text-[11px] font-medium text-emerald-600 mt-2 block">Siap ditempati</span>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>

                    <!-- Kamar Penuh -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200 flex justify-between items-start shadow-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Kamar Penuh</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $kamarPenuh }}</h3>
                            <span class="text-[11px] font-medium text-slate-400 mt-2 block">Terisi 3 penghuni</span>
                        </div>
                        <div class="w-10 h-10 bg-slate-100 text-slate-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        </div>
                    </div>

                    <!-- Pending Verifikasi -->
                    <div class="bg-white p-5 rounded-xl border border-slate-200 flex justify-between items-start shadow-sm">
                        <div>
                            <p class="text-xs font-semibold text-slate-500">Pending Reservasi</p>
                            <h3 class="text-2xl font-bold text-slate-900 mt-1">{{ $pendingReservasi }}</h3>
                            <span class="text-[11px] font-medium text-amber-600 mt-2 block">Menunggu verifikasi</span>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <!-- Main Grid: Reservasi Terbaru & Activity Feed -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Kolom Kiri: Reservasi Terbaru -->
                    <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h2 class="text-base font-bold text-slate-900">Pengajuan Reservasi Terbaru</h2>
                                <p class="text-xs text-slate-500">Menampilkan hingga 5 reservasi terakhir.</p>
                            </div>
                            <span class="text-xs bg-slate-100 text-slate-600 font-medium px-2.5 py-1 rounded-full">{{ $pendingReservasi }} Menunggu</span>
                        </div>

                        @if($latestReservasi->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                                    <thead class="bg-slate-50 text-slate-700 uppercase text-[11px] font-bold border-b border-slate-200">
                                        <tr>
                                            <th class="py-3.5 px-4">Kode</th>
                                            <th class="py-3.5 px-4">Penghuni</th>
                                            <th class="py-3.5 px-4">Kamar</th>
                                            <th class="py-3.5 px-4">Pengajuan</th>
                                            <th class="py-3.5 px-4">Durasi</th>
                                            <th class="py-3.5 px-4">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($latestReservasi as $reservasi)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $reservasi->kode_reservasi }}</td>
                                                <td class="py-3.5 px-4">
                                                    <span class="font-bold text-slate-900 block">{{ $reservasi->user->nama ?? '-' }}</span>
                                                    <span class="text-[11px] text-slate-400 block">{{ $reservasi->user->nim_nik ?? '-' }}</span>
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    @if($reservasi->kamar)
                                                        <span class="font-bold text-slate-800">{{ $reservasi->kamar->nomor_kamar }}</span>
                                                        <span class="text-[11px] text-slate-400 block">Blok {{ $reservasi->kamar->blok }}</span>
                                                    @else
                                                        <span class="text-amber-600 italic font-medium">Belum ditentukan</span>
                                                    @endif
                                                </td>
                                                <td class="py-3.5 px-4">{{ \Carbon\Carbon::parse($reservasi->tanggal_pengajuan)->format('d M Y') }}</td>
                                                <td class="py-3.5 px-4 font-medium">{{ $reservasi->durasi_sewa }}</td>
                                                <td class="py-3.5 px-4">
                                                    @if($reservasi->status === 'pending')
                                                        <span class="px-2.5 py-1 text-[11px] font-bold bg-amber-100 text-amber-800 rounded-full">Menunggu</span>
                                                    @elseif($reservasi->status === 'approved')
                                                        <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-800 rounded-full">Disetujui</span>
                                                    @elseif($reservasi->status === 'rejected')
                                                        <span class="px-2.5 py-1 text-[11px] font-bold bg-red-100 text-red-800 rounded-full">Ditolak</span>
                                                    @else
                                                        <span class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 text-slate-600 rounded-full">Dibatalkan</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('admin.reservasi.index') }}" class="text-xs font-semibold text-[#ed1c24] hover:text-red-700">Lihat semua reservasi →</a>
                            </div>
                        @else
                            <div class="py-12 text-center text-slate-400 text-sm">
                                <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                Belum ada pengajuan reservasi masuk.
                            </div>
                        @endif
                    </div>

                    <!-- Kolom Kanan: Status Kamar & Log Aktivitas -->
                    <div class="space-y-6">

                        <!-- Distribution Card -->
                        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                            <h2 class="text-base font-bold text-slate-900 mb-4">Status Kamar</h2>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="flex items-center text-slate-600">
                                        <span class="w-3 h-3 bg-emerald-500 rounded-full mr-2"></span> Kamar Tersedia
                                    </span>
                                    <span class="font-bold text-slate-800">{{ $kamarTersedia }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="flex items-center text-slate-600">
                                        <span class="w-3 h-3 bg-slate-700 rounded-full mr-2"></span> Kamar Penuh
                                    </span>
                                    <span class="font-bold text-slate-800">{{ $kamarPenuh }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs sm:text-sm">
                                    <span class="flex items-center text-slate-600">
                                        <span class="w-3 h-3 bg-amber-500 rounded-full mr-2"></span> Dalam Perbaikan
                                    </span>
                                    <span class="font-bold text-slate-800">{{ $kamarPerbaikan }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Activity Feed -->
                        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm">
                            <h2 class="text-base font-bold text-slate-900 mb-4">Aktivitas Terkini</h2>
                            <div class="space-y-4 text-xs">
                                <div class="flex items-start space-x-3">
                                    <div class="w-2 h-2 mt-1.5 rounded-full bg-[#ed1c24] shrink-0"></div>
                                    <div>
                                        <p class="font-bold text-slate-800">Sistem Asrama Aktif</p>
                                        <p class="text-slate-500 text-[11px]">Siap mengelola data kamar dan reservasi.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </main>
        </div>

    </div>
</body>
</html>