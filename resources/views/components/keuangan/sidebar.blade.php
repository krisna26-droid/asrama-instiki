@props(['active' => ''])

<aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 hidden md:flex">
    <div>
        <!-- Brand Header -->
        <div class="h-20 flex items-center px-6 border-b border-slate-100">
            <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-9 w-auto">
            <div class="ml-3 border-l border-slate-200 pl-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Keuangan</span>
                <span class="text-[10px] text-slate-500">INSTIKI Asrama</span>
            </div>
        </div>

        <!-- Navigasi Menu -->
        <div class="px-4 py-6">
            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-3">Menu Utama</span>
            <nav class="space-y-1">
                <!-- Dashboard -->
                <a href="{{ route('keuangan.dashboard') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'dashboard' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <!-- Verifikasi Pembayaran -->
                <a href="{{ route('keuangan.pembayaran.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'pembayaran' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Verifikasi Pembayaran
                </a>

                <!-- Riwayat Transaksi -->
                <a href="{{ route('keuangan.riwayat.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'riwayat' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Riwayat Pembayaran
                </a>

                <!-- Laporan Keuangan -->
                <a href="{{ route('keuangan.laporan.index') }}" 
                   class="flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition {{ $active === 'laporan' ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan Keuangan
                </a>
            </nav>
        </div>
    </div>

    <!-- Footer Sidebar -->
    <div class="p-4 border-t border-slate-200">
        <div class="bg-slate-50 p-3 rounded-lg">
            <p class="text-xs font-bold text-slate-800">Admin Keuangan</p>
            <p class="text-[10px] text-slate-500">v1.0 Finance Ops</p>
        </div>
    </div>
</aside>