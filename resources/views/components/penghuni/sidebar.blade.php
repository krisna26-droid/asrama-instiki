@props(['active' => ''])

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 hidden md:flex">
    <div>
        <!-- Header Brand -->
        <div class="h-20 flex items-center px-6 border-b border-slate-100">
            <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-9 w-auto">
            <div class="ml-3 border-l border-slate-200 pl-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Penghuni</span>
                <span class="text-[10px] text-slate-500">Portal Mahasiswa</span>
            </div>
        </div>

        <!-- Navigasi Menu -->
        <div class="px-4 py-6">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-3">Menu Utama</span>
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'dashboard' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <!-- Kamar Tersedia -->
                <a href="{{ route('penghuni.kamar.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'kamar' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5"></path></svg>
                    Kamar Tersedia
                </a>

                <!-- Reservasi Saya -->
                <a href="{{ route('penghuni.reservasi.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'reservasi' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Reservasi Saya
                </a>

                <!-- Pembayaran -->
                <a href="{{ route('penghuni.pembayaran.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'pembayaran' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                    Pembayaran
                </a>

                <!-- Profil -->
                <a href="{{ route('profile.edit') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'profil' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Profil Saya
                </a>
            </nav>
        </div>
    </div>

    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-slate-200">
        <div class="bg-slate-50 p-3 rounded-lg">
            <p class="text-xs font-bold text-slate-800">Penghuni Asrama</p>
            <p class="text-[10px] text-slate-500">v1.0 Mahasiswa Portal</p>
        </div>
    </div>
</aside>