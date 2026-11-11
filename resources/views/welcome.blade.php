<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistem Informasi Pengelolaan & Reservasi Asrama INSTIKI</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            /* Galeri */
            .gal-tile img { transition: transform .5s ease; }
            .gal-tile:hover img { transform: scale(1.06); }
            .gal-cap { opacity: 0; transform: translateY(.5rem); transition: opacity .3s ease, transform .3s ease; }
            .gal-tile:hover .gal-cap, .gal-tile:focus-within .gal-cap { opacity: 1; transform: none; }
            .gal-pill { transition: background-color .2s ease, border-color .2s ease; }
            .gal-tile:hover .gal-pill, .gal-tile:focus-visible .gal-pill { background: #ed1c24; border-color: #ed1c24; }
            @media (hover: none) { .gal-cap { opacity: 1; transform: none; } }

            /* Kontak */
            .lift { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
            .lift:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(15, 23, 42, .18); border-color: #cbd5e1; }
            .contact-icon { transition: background-color .2s ease, color .2s ease; }
            .lift:hover .contact-icon { background: #ed1c24; color: #fff; }

            @media (prefers-reduced-motion: reduce) {
                .gal-tile img, .gal-cap, .gal-pill, .lift, .contact-icon { transition: none; }
                .gal-tile:hover img, .lift:hover { transform: none; }
            }
        </style>
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
                <div class="flex items-center gap-2 sm:gap-3">
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
                        <a href="{{ route('login') }}" class="rounded px-3 py-2 text-xs font-semibold text-slate-700 transition hover:text-[#ed1c24] sm:text-sm">Login</a>
                        <a href="{{ route('register') }}" class="rounded bg-[#ed1c24] px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700 sm:px-4 sm:text-sm">Registrasi</a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section (Clean Solid Dark, Red Accent, No Gradients) -->
        <section class="relative isolate overflow-hidden min-h-[750px] sm:min-h-[850px] flex items-center bg-slate-900 text-white py-16 sm:py-24 border-b border-slate-800" style="min-height: 650px;">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0" style="background-image: url('{{ asset('image/asrama-bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
            <!-- Dark Overlay -->
            <div class="absolute inset-0 z-10" style="background: linear-gradient(to bottom, rgba(2, 6, 23, 0.65), rgba(2, 6, 23, 0.8));"></div>
            <!-- Hero Content -->
            <div class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-block py-1 px-3 rounded bg-[#ed1c24]/20 border border-[#ed1c24]/30 text-red-400 text-xs font-bold tracking-widest uppercase mb-6">Official Housing Portal INSTIKI</span>
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight mb-6 leading-snug">Sistem Informasi Pengelolaan & Reservasi<br>
                    <span class="text-[#ed1c24]">Asrama Kampus INSTIKI</span>
                </h1>

                <p class="max-w-2xl mx-auto text-sm sm:text-base text-slate-200 mb-8 leading-relaxed font-normal">
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

                        <a href="{{ url($targetUrl) }}"
                        class="w-full sm:w-auto px-6 py-3 bg-[#ed1c24] text-white font-semibold rounded hover:bg-red-700 transition text-sm">
                            Masuk ke Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                        class="w-full sm:w-auto px-6 py-3 bg-slate-800/90 border border-slate-600 text-white font-semibold rounded hover:bg-slate-700 transition text-sm">
                            Masuk Sistem
                        </a>
                    @endauth
                </div>

            </div>
            {{-- Info ketersediaan (pojok kanan bawah hero) --}}
            <div class="absolute z-20" style="right: 1.5rem; bottom: 1.5rem;">
                <div class="flex text-white rounded" style="background: rgba(2, 6, 23, 0.7); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(4px);">
                    <div class="px-4 py-2.5 text-right">
                        <p class="text-[10px] uppercase tracking-wider text-slate-300">Kamar Tersedia</p>
                        <p class="text-lg font-bold leading-tight sm:text-xl">{{ $schemaReady ? number_format($kapasitasTersedia) : '—' }}</p>
                    </div>
                    <div class="px-4 py-2.5 text-right" style="border-left: 1px solid rgba(255, 255, 255, 0.2);">
                        <p class="text-[10px] uppercase tracking-wider text-slate-300">Penghuni</p>
                        <p class="text-lg font-bold leading-tight sm:text-xl">{{ $schemaReady ? number_format($penghuniAktif) : '—' }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if(!empty($cabangSummaries))
            <section class="bg-white py-12 sm:py-16" aria-labelledby="cabang-asrama-title">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-7">
                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#ed1c24]">Beberapa titik lokasi</p>
                        <h2 id="cabang-asrama-title" class="mt-2 text-2xl font-bold text-slate-900">Pilih lokasi asrama yang sesuai</h2>
                        <p class="mt-2 text-sm text-slate-600">Pengajuan tidak memilih kamar. Admin Asrama akan menentukan cabang dan sistem mengalokasikan kamar setelah pengajuan ditinjau.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach($cabangSummaries as $cabang)
                            <article class="rounded-xl border border-slate-200 bg-slate-50 p-5 shadow-sm">
                                <span class="font-mono text-xs text-slate-400">{{ $cabang['kode'] }}</span>
                                <h3 class="mt-1 text-lg font-bold text-slate-900">{{ $cabang['nama'] }}</h3>
                                <p class="mt-2 text-sm text-slate-600">{{ $cabang['alamat'] }}</p>
                                <dl class="mt-4 grid grid-cols-3 gap-2 border-t border-slate-200 pt-4 text-center">
                                    <div><dt class="text-[10px] text-slate-500">Kapasitas</dt><dd class="mt-1 font-bold text-slate-900">{{ number_format($cabang['totalKapasitas']) }}</dd></div>
                                    <div><dt class="text-[10px] text-slate-500">Aktif</dt><dd class="mt-1 font-bold text-blue-800">{{ $cabang['schemaReady'] ? number_format($cabang['penghuniAktif']) : '—' }}</dd></div>
                                    <div><dt class="text-[10px] text-slate-500">Tersedia</dt><dd class="mt-1 font-bold text-emerald-700">{{ $cabang['schemaReady'] ? number_format($cabang['kapasitasTersedia']) : '—' }}</dd></div>
                                </dl>
                                <div class="mt-4 flex items-center justify-between gap-3">
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $cabang['schemaReady'] && $cabang['kapasitasTersedia'] > 0 ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">{{ $cabang['schemaReady'] ? $cabang['statusKetersediaan'] : 'Data diperbarui setelah verifikasi' }}</span>
                                    @if($cabang['maps_url'])<a href="{{ $cabang['maps_url'] }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-blue-700">Lihat peta ↗</a>@endif
                                </div>
                                @if($cabang['telepon'] || $cabang['whatsapp'])
                                    <div class="mt-3 flex flex-wrap gap-3 text-xs">
                                        @if($cabang['telepon'])<a class="font-medium text-slate-600" href="tel:{{ preg_replace('/[^0-9+]/', '', $cabang['telepon']) }}">Telepon {{ $cabang['telepon'] }}</a>@endif
                                        @if($cabang['whatsapp'])<a class="font-medium text-emerald-700" href="https://wa.me/{{ preg_replace('/\D+/', '', $cabang['whatsapp']) }}" target="_blank" rel="noopener noreferrer">WhatsApp</a>@endif
                                    </div>
                                @endif
                            </article>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Galeri fasilitas dan informasi kontak --}}
        @php
            // Maksimal 2 foto fasilitas; sisanya diisi foto bawaan supaya kolase tetap penuh
            $tiles = $facilities->take(2)->map(fn ($f) => [
                'src'     => '/storage/' . ltrim($f->gambar_path, '/'),
                'title'   => $f->judul,
                'caption' => $f->caption,
            ])->values()->all();

            $fallbackTiles = [
                ['src' => asset('image/asrama-bg.png'), 'title' => 'Tampilan Asrama', 'caption' => 'Bangunan Asrama Kampus INSTIKI'],
                ['src' => asset('image/asrama-background.jpg'), 'title' => 'Suasana Asrama', 'caption' => 'Lingkungan di sekitar asrama'],
            ];
            while (count($tiles) < 2) {
                $tiles[] = $fallbackTiles[count($tiles)];
            }

            $whatsappNumber = preg_replace('/\D+/', '', $contact?->whatsapp ?? '');
            if (str_starts_with($whatsappNumber, '0')) {
                $whatsappNumber = '62' . substr($whatsappNumber, 1);
            }
        @endphp

        <section id="tentang-asrama" class="scroll-mt-24 bg-slate-50 py-16 sm:py-20">
            <div class="mx-auto grid max-w-7xl gap-10 px-4 sm:px-6 lg:grid-cols-2 lg:items-center lg:gap-16 lg:px-8">

                <!-- Galeri -->
                <div>
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#ed1c24]">Lihat lebih dekat</span>
                    <h2 class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Ruang nyaman untuk bertumbuh</h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-slate-600">Kenali fasilitas dan suasana Asrama Kampus INSTIKI.</p>

                    <div class="mt-7 grid grid-cols-2 gap-3" style="grid-template-rows: repeat(2, minmax(11rem, 1fr)); min-height: 24rem;">
                        @foreach($tiles as $tile)
                            <figure class="gal-tile relative overflow-hidden rounded-xl bg-slate-200 shadow-sm" style="{{ $loop->first ? 'grid-row: span 2 / span 2;' : '' }}">
                                <img src="{{ $tile['src'] }}" alt="{{ $tile['title'] }}" class="absolute inset-0 h-full w-full object-cover">
                                <figcaption class="gal-cap absolute inset-x-0 bottom-0 p-4 text-white" style="background: linear-gradient(to top, rgba(2, 6, 23, 0.8), rgba(2, 6, 23, 0));">
                                    <p class="text-sm font-semibold leading-tight">{{ $tile['title'] }}</p>
                                    <p class="mt-0.5 text-xs text-slate-200">{{ $tile['caption'] }}</p>
                                </figcaption>
                            </figure>
                        @endforeach

                        <a href="{{ route('asrama.detail') }}" class="gal-tile relative overflow-hidden rounded-xl bg-slate-200 shadow-sm" aria-label="Lihat detail asrama">
                            <img src="{{ asset('image/asrama-background.jpg') }}" alt="Detail Asrama Kampus INSTIKI" class="absolute inset-0 h-full w-full object-cover">
                            <div class="absolute inset-0 bg-slate-950/40"></div>
                            <span class="gal-pill absolute bottom-4 right-4 inline-flex items-center gap-1.5 rounded-full border border-white/40 bg-slate-950/55 px-4 py-2 text-xs font-semibold text-white backdrop-blur-sm">
                                Lihat detail
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    </div>
                </div>

                <!-- Kontak -->
                <aside class="rounded-xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#ed1c24]">Kami siap membantu</span>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Ada yang ingin ditanyakan?</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600">Hubungi pengelola asrama untuk bertanya tentang fasilitas, proses reservasi, atau informasi lainnya.</p>

                    <div class="mt-6 space-y-3">

                        {{-- WhatsApp --}}
                        @if($whatsappNumber)
                            <a href="https://wa.me/{{ $whatsappNumber }}" target="_blank" rel="noopener noreferrer"
                               class="lift flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 px-5 py-3 text-sm font-semibold text-white hover:bg-emerald-700">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                                Hubungi via WhatsApp
                            </a>
                        @else
                            <div class="rounded-lg bg-slate-50 p-4 text-sm text-slate-600">Kontak WhatsApp belum diatur. Silakan cek kembali nanti.</div>
                        @endif

                        {{-- Telepon --}}
                        @if($contact?->telepon)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact->telepon) }}" class="lift flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3.5">
                                <span class="contact-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-xs text-slate-500">Telepon</span>
                                    <span class="block truncate text-sm font-semibold text-slate-800">{{ $contact->telepon }}</span>
                                </span>
                            </a>
                        @endif

                        {{-- Email --}}
                        @if($contact?->email)
                            <a href="mailto:{{ $contact->email }}" class="lift flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-3.5">
                                <span class="contact-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-xs text-slate-500">Email</span>
                                    <span class="block break-all text-sm font-semibold text-slate-800">{{ $contact->email }}</span>
                                </span>
                            </a>
                        @endif

                        {{-- Alamat (dibuka di Google Maps) --}}
                        @if($contact?->alamat)
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->alamat) }}" target="_blank" rel="noopener noreferrer" class="lift flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-3.5">
                                <span class="contact-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </span>
                                <span class="min-w-0">
                                    <span class="block text-xs text-slate-500">Alamat</span>
                                    <span class="block whitespace-pre-line text-sm font-semibold text-slate-800">{{ $contact->alamat }}</span>
                                </span>
                            </a>
                        @endif
                    </div>
                </aside>
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
