@props([
    'searchRoute' => null,
    'placeholder' => 'Cari kamar, penghuni, atau reservasi...'
])

<header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-10">
    <!-- Form Pencarian / Search Bar -->
    <div class="w-72 sm:w-96">
        @if($searchRoute)
            <form action="{{ $searchRoute }}" method="GET">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
                </div>
            </form>
        @else
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" placeholder="{{ $placeholder }}" class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
            </div>
        @endif
    </div>

    <!-- Profil User & Logout -->
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