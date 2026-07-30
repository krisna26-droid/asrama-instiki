<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistem Informasi Pengelolaan & Reservasi Asrama INSTIKI</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white text-slate-900 font-sans antialiased selection:bg-[#ed1c24] selection:text-white">

        <!-- Navbar (Fully Responsive) -->
        <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
                <!-- Logo & Brand -->
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-8 sm:h-10 w-auto object-contain">
                    <div class="border-l pl-3 border-slate-300 hidden sm:block">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Asrama Kampus</span>
                        <span class="text-[10px] text-slate-500">Institut Bisnis dan Teknologi Indonesia</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center space-x-2 sm:space-x-4">
                    @auth
                        @php
                            $role = auth()->user()->role;
                            $targetUrl = '/dashboard';
                            if ($role === 'admin_asrama') {
                                $targetUrl = '/admin/dashboard';
                            } elseif ($role === 'admin_keuangan') {
                                $targetUrl = '/keuangan/dashboard';
                            }
                        @endphp
                        <a href="{{ url($targetUrl) }}" class="px-4 py-2 bg-[#ed1c24] text-white text-xs sm:text-sm font-semibold rounded hover:bg-red-700 transition">Dashboard Saya</a>
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-slate-700 text-xs sm:text-sm font-semibold hover:text-[#ed1c24] transition">Log in</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="px-4 py-2 bg-[#ed1c24] text-white text-xs sm:text-sm font-semibold rounded hover:bg-red-700 transition">Daftar Akun</a>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section (Clean Solid Dark, Red Accent, No Gradients) -->
        <section class="bg-slate-900 text-white py-16 sm:py-24 border-b border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-block py-1 px-3 rounded bg-[#ed1c24]/20 border border-[#ed1c24]/30 text-red-400 text-xs font-bold tracking-widest uppercase mb-6">
                    Official Housing Portal INSTIKI
                </span>
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-6 leading-snug">
                    Sistem Informasi Pengelolaan & Reservasi <br><span class="text-[#ed1c24]">Asrama Kampus INSTIKI</span>
                </h1>
                <p class="max-w-2xl mx-auto text-sm sm:text-base text-slate-300 mb-8 leading-relaxed font-normal">
                    Hunian sementara yang nyaman, aman, dan mendukung produktivitas akademik mahasiswa baru INSTIKI. Lakukan reservasi kamar secara online dengan mudah dan transparan.
                </p>
                <div class="flex flex-col sm:flex-row justify-center items-center gap-3 sm:gap-4">
                    @auth
                        @php
                            $role = auth()->user()->role;
                            $targetUrl = '/dashboard';
                            if ($role === 'admin_asrama') {
                                $targetUrl = '/admin/dashboard';
                            } elseif ($role === 'admin_keuangan') {
                                $targetUrl = '/keuangan/dashboard';
                            }
                        @endphp
                        <a href="{{ url($targetUrl) }}" class="w-full sm:w-auto px-6 py-3 bg-[#ed1c24] text-white font-semibold rounded hover:bg-red-700 transition text-sm">Masuk ke Dashboard</a>
                    @else
                        <a href="{{ route('register') }}" class="w-full sm:w-auto px-6 py-3 bg-[#ed1c24] text-white font-semibold rounded hover:bg-red-700 transition text-sm">Mulai Pendaftaran Asrama</a>
                        <a href="{{ route('login') }}" class="w-full sm:w-auto px-6 py-3 bg-slate-800 border border-slate-700 text-slate-200 font-semibold rounded hover:bg-slate-700 transition text-sm">Masuk Sistem</a>
                    @endauth
                </div>
            </div>
        </section>

        <!-- Keunggulan Section (Responsive Grid, Clean Flat Cards) -->
        <section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 sm:mb-16">
                <h2 class="text-xl sm:text-3xl font-bold text-slate-900">Mengapa Tinggal di Asrama INSTIKI?</h2>
                <p class="text-slate-600 mt-2 text-xs sm:text-sm">Fasilitas penunjang terbaik untuk masa adaptasi dan kenyamanan perkuliahan Anda.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Card 1 -->
                <div class="bg-white p-6 rounded-lg border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-red-50 text-[#ed1c24] rounded flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="font-bold text-sm sm:text-base mb-2 text-slate-900">Aman & Nyaman</h3>
                    <p class="text-slate-600 text-xs leading-relaxed">Keamanan terjaga dengan lingkungan yang kondusif untuk konsentrasi belajar.</p>
                </div>
                <!-- Card 2 -->
                <div class="bg-white p-6 rounded-lg border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-red-50 text-[#ed1c24] rounded flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <h3 class="font-bold text-sm sm:text-base mb-2 text-slate-900">Fasilitas Lengkap</h3>
                    <p class="text-slate-600 text-xs leading-relaxed">Kamar nyaman dan terstruktur untuk mendukung kehidupan mandiri mahasiswa.</p>
                </div>
                <!-- Card 3 -->
                <div class="bg-white p-6 rounded-lg border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-red-50 text-[#ed1c24] rounded flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-sm sm:text-base mb-2 text-slate-900">Lingkungan Positif</h3>
                    <p class="text-slate-600 text-xs leading-relaxed">Mendukung kemandirian, kedisiplinan, dan interaksi sosial antar mahasiswa.</p>
                </div>
                <!-- Card 4 -->
                <div class="bg-white p-6 rounded-lg border border-slate-200 text-center">
                    <div class="w-12 h-12 bg-red-50 text-[#ed1c24] rounded flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h3 class="font-bold text-sm sm:text-base mb-2 text-slate-900">Dekat Kampus</h3>
                    <p class="text-slate-600 text-xs leading-relaxed">Lokasi strategis di sekitar area kampus INSTIKI, memudahkan akses akademik.</p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-6 text-center text-slate-500 text-xs">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p>&copy; 2026 Institut Bisnis dan Teknologi Indonesia (INSTIKI). All rights reserved.</p>
                <div class="flex space-x-4 text-slate-600 font-medium">
                    <a href="https://instiki.ac.id" target="_blank" class="hover:text-[#ed1c24] transition">Portal Utama</a>
                    <a href="https://sads.instiki.ac.id" target="_blank" class="hover:text-[#ed1c24] transition">SADS</a>
                    <a href="https://elsa.instiki.ac.id" target="_blank" class="hover:text-[#ed1c24] transition">ELSA</a>
                </div>
            </div>
        </footer>

    </body>
</html>