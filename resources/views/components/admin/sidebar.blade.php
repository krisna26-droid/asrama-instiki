@props(['active' => ''])

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
                <a href="{{ route('admin.dashboard') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'dashboard' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <!-- Manajemen Kamar -->
                <a href="{{ route('kamar.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'kamar' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Manajemen Kamar
                </a>

                <!-- Verifikasi Reservasi -->
                <a href="{{ route('admin.reservasi.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'reservasi' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Verifikasi Reservasi
                </a>

                <!-- Penghuni Aktif -->
                <a href="{{ route('admin.penghuni.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'penghuni' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Penghuni Aktif
                </a>

                <!-- Laporan -->
                <a href="{{ route('admin.laporan.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'laporan' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan
                </a>
            </nav>
        </div>
    </div>

    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-slate-200">
        <div class="bg-slate-50 p-3 rounded-lg flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-slate-800">Admin Asrama</p>
                <p class="text-[10px] text-slate-500">v1.0 Operational</p>
            </div>
        </div>
    </div>
</aside>