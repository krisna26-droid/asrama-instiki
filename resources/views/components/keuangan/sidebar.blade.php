@props(['active' => ''])

@php
    $menu = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => 'keuangan.dashboard',
         'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
        ['key' => 'pembayaran', 'label' => 'Verifikasi Pembayaran', 'route' => 'keuangan.pembayaran.index',
         'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
        ['key' => 'riwayat', 'label' => 'Riwayat Pembayaran', 'route' => 'keuangan.riwayat.index',
         'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['key' => 'laporan', 'label' => 'Laporan Keuangan', 'route' => 'keuangan.laporan.index',
         'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
    ];
@endphp

<div x-data="{ open: false }" @toggle-sidebar.window="open = !open" @keydown.escape.window="open = false" class="adm-wrap">

    <!-- Overlay (mobile) -->
    <div x-cloak x-show="open" x-transition.opacity @click="open = false" class="adm-overlay adm-only-mobile"></div>

    <aside class="adm-sidebar flex flex-col justify-between border-r border-slate-200 bg-white" :class="{ 'is-open': open }">
        <div class="min-h-0 flex-1 overflow-y-auto">
            <!-- Brand Header -->
            <div class="flex h-16 items-center border-b border-slate-100 px-5 sm:h-20 sm:px-6">
                <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-8 w-auto sm:h-9">
                <div class="ml-3 border-l border-slate-200 pl-3">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-800">Keuangan</span>
                    <span class="text-[10px] text-slate-500">INSTIKI Asrama</span>
                </div>
                <button type="button" class="ml-auto rounded-lg p-2 text-slate-500 hover:bg-slate-100 adm-only-mobile" @click="open = false" aria-label="Tutup menu">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                </button>
            </div>

            <!-- Navigasi Menu -->
            <div class="px-3 py-6">
                <span class="mb-3 block px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">Menu Utama</span>
                <nav class="space-y-1">
                    @foreach ($menu as $item)
                        @php $isActive = $active === $item['key']; @endphp
                        <a href="{{ route($item['route']) }}"
                           class="group flex items-center rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $isActive ? 'bg-red-50 text-[#ed1c24] font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <svg class="mr-3 h-5 w-5 shrink-0 {{ $isActive ? 'text-[#ed1c24]' : 'text-slate-400 group-hover:text-slate-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"></path>
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>
            </div>
        </div>

        <!-- Footer Sidebar -->
        <div class="shrink-0 border-t border-slate-200 p-4">
            <div class="rounded-lg bg-slate-50 p-3">
                <p class="text-xs font-bold text-slate-800">Admin Keuangan</p>
                <p class="text-[10px] text-slate-500">v1.0 Finance Ops</p>
            </div>
        </div>
    </aside>
</div>