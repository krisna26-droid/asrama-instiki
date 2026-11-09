@props(['active' => ''])

@php
    $menu = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'route' => 'dashboard',
         'icon' => 'M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z'],
        ['key' => 'kamar', 'label' => 'Kamar Tersedia', 'route' => 'penghuni.kamar.index',
         'icon' => 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5'],
        ['key' => 'reservasi', 'label' => 'Reservasi Saya', 'route' => 'penghuni.reservasi.index',
         'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ['key' => 'pembayaran', 'label' => 'Pembayaran', 'route' => 'penghuni.pembayaran.index',
         'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
        ['key' => 'profil', 'label' => 'Profil Saya', 'route' => 'profile.edit',
         'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z'],
    ];
@endphp

@once
    <style>
        /* Layout sidebar ditulis manual agar tidak bergantung pada hasil build Tailwind */
        .pen-wrap { display: contents; }
        .pen-sidebar { display: flex; flex-direction: column; justify-content: space-between; width: 16rem; flex-shrink: 0; align-self: flex-start; position: sticky; top: 0; height: 100vh; background: #fff; border-right: 1px solid #e2e8f0; }
        .pen-overlay { position: fixed; inset: 0; z-index: 30; background: rgba(15, 23, 42, .5); }
        @media (max-width: 767px) {
            .pen-sidebar { position: fixed; top: 0; bottom: 0; left: 0; height: auto; z-index: 40; transform: translateX(-100%); transition: transform .2s ease; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .35); }
            .pen-sidebar.is-open { transform: translateX(0); }
        }
        @media (min-width: 768px) {
            .pen-only-mobile { display: none !important; }
        }
        [x-cloak] { display: none !important; }
    </style>
@endonce

<div x-data="{ open: false }" @toggle-sidebar.window="open = !open" @keydown.escape.window="open = false" class="pen-wrap">

    <!-- Overlay (mobile) -->
    <div x-cloak x-show="open" x-transition.opacity @click="open = false" class="pen-overlay pen-only-mobile"></div>

    <aside class="pen-sidebar" :class="{ 'is-open': open }">
        <div class="min-h-0 flex-1 overflow-y-auto">
            <!-- Brand Header -->
            <div class="flex h-16 items-center border-b border-slate-100 px-5 sm:h-20 sm:px-6">
                <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-8 w-auto sm:h-9">
                <div class="ml-3 border-l border-slate-200 pl-3">
                    <span class="block text-xs font-bold uppercase tracking-wider text-slate-800">Penghuni</span>
                    <span class="block whitespace-nowrap text-[10px] text-slate-500">Portal Mahasiswa</span>
                </div>
                <button type="button" class="pen-only-mobile ml-auto rounded-lg p-2 text-slate-500 hover:bg-slate-100" @click="open = false" aria-label="Tutup menu">
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
                <p class="text-xs font-bold text-slate-800">Penghuni Asrama</p>
                <p class="text-[10px] text-slate-500">v1.0 Mahasiswa Portal</p>
            </div>
        </div>
    </aside>
</div>