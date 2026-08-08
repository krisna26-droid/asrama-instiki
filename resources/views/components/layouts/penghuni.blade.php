@props([
    'title' => 'Dashboard Penghuni - Asrama INSTIKI',
    'activeMenu' => 'dashboard'
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" {{ $attributes }}>
    <div class="min-h-screen flex">

        <!-- Sidebar Component -->
        <x-penghuni.sidebar :active="$activeMenu" />

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Navbar Topbar -->
            <header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-10">
                <div class="w-72 sm:w-96">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" placeholder="Cari kamar, reservasi, pembayaran..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3 text-right">
                        <div>
                            <span class="block text-sm font-bold text-slate-800">{{ auth()->user()->nama }}</span>
                            <span class="block text-xs text-slate-500">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold flex items-center justify-center text-sm border border-blue-200">
                            {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
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

            <!-- Body Content -->
            <main class="p-6 sm:p-8 space-y-6 flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>