@props([
    'title' => 'Dashboard Penghuni - Asrama INSTIKI',
    'activeMenu' => 'dashboard',
])

@php
    $pageTitles = [
        'dashboard'  => 'Dashboard',
        'kamar'      => 'Kapasitas Asrama',
        'reservasi'  => 'Reservasi Saya',
        'pembayaran' => 'Pembayaran',
        'profil'     => 'Profil Saya',
    ];
    $pageTitle = $pageTitles[$activeMenu] ?? 'Portal Penghuni';
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>[x-cloak] { display: none !important; }</style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased" {{ $attributes }}>
    <div class="flex min-h-screen">

        <!-- Sidebar Component -->
        <x-penghuni.sidebar :active="$activeMenu" />

        <!-- Area Konten Utama -->
        <div class="flex min-w-0 flex-1 flex-col">

            <!-- Topbar -->
            <header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:h-20 sm:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="pen-only-mobile inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50" @click="$dispatch('toggle-sidebar')" aria-label="Buka menu">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div class="min-w-0 leading-tight">
                        <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Asrama INSTIKI</span>
                        <span class="block truncate text-sm font-semibold text-slate-800 sm:text-base">{{ $pageTitle }}</span>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-4">
                    <div class="hidden items-center gap-3 border-l border-slate-200 pl-4 text-right sm:flex">
                        <div class="leading-tight">
                            <span class="block text-sm font-bold text-slate-800">{{ auth()->user()->nama }}</span>
                            <span class="block text-xs text-slate-500">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="flex h-9 w-9 items-center justify-center rounded-full border border-red-200 bg-red-100 text-sm font-bold text-[#ed1c24]">
                            {{ mb_strtoupper(mb_substr(auth()->user()->nama, 0, 1)) }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Log Out" class="rounded-lg p-2 text-slate-500 transition hover:bg-slate-100 hover:text-[#ed1c24]">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Body Content -->
            <main class="min-w-0 flex-1 space-y-6 overflow-y-auto p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>