@props([
    'pageTitle' => 'Dashboard',
    'searchRoute' => null,
    'placeholder' => 'Cari transaksi, penghuni, atau nominal...'
])

<header class="sticky top-0 z-30 flex h-16 items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 sm:h-20 sm:px-6">
    <!-- Kiri: tombol menu (mobile) + judul halaman -->
    <div class="flex min-w-0 items-center gap-3">
        <button type="button" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 adm-only-mobile" @click="$dispatch('toggle-sidebar')" aria-label="Buka menu">
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="min-w-0 leading-tight">
            <span class="block text-[10px] font-bold uppercase tracking-widest text-slate-400">Asrama INSTIKI</span>
            <span class="block truncate text-sm font-semibold text-slate-800 sm:text-base">{{ $pageTitle }}</span>
        </div>
    </div>

    <!-- Kanan: pencarian (opsional) + profil -->
    <div class="flex shrink-0 items-center gap-2 sm:gap-4">
        @if($searchRoute)
            <form action="{{ $searchRoute }}" method="GET" class="hidden w-64 lg:block">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}" class="w-full rounded-lg border border-slate-200 bg-slate-50 py-2 pl-9 pr-4 text-sm focus:border-[#ed1c24] focus:bg-white focus:ring-[#ed1c24]">
                </div>
            </form>
        @endif

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