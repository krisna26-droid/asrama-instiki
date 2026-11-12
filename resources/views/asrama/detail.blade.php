@php
    $galleryItems = $facilities->map(fn ($f) => [
        'src'     => '/storage/' . ltrim($f->gambar_path, '/'),
        'title'   => $f->judul,
        'caption' => $f->caption,
    ])->values()->all();

    $whatsappNumber = preg_replace('/\D+/', '', $contact?->whatsapp ?? '');
    if (str_starts_with($whatsappNumber, '0')) {
        $whatsappNumber = '62' . substr($whatsappNumber, 1);
    }

    $hasContact = $contact && ($contact->whatsapp || $contact->telepon || $contact->email || $contact->alamat);
@endphp
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Detail Asrama Kampus INSTIKI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }

        /* Daftar fasilitas: baris foto dan teks bergantian */
        .dt-list { display: grid; gap: 4.5rem; }
        @media (min-width: 1024px) {
            .dt-list { gap: 6rem; }
            .dt-row:nth-child(even) .dt-media { order: 2; }
        }

        /* Foto yang bisa diperbesar */
        .dt-media img { transition: transform .6s ease; }
        .dt-media:hover img { transform: scale(1.04); }
        .dt-zoom { opacity: 0; transform: translateY(.25rem); transition: opacity .25s ease, transform .25s ease; }
        .dt-media:hover .dt-zoom, .dt-media:focus-visible .dt-zoom { opacity: 1; transform: none; }
        @media (hover: none) { .dt-zoom { opacity: 1; transform: none; } }
        .dt-media:focus-visible { outline: 2px solid #ed1c24; outline-offset: 3px; }

        /* Lightbox */
        .dt-lb { position: fixed; inset: 0; z-index: 100; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 1rem; background: rgba(2, 6, 23, .94); }
        .dt-lb-img { max-width: min(92vw, 1200px); max-height: 76vh; object-fit: contain; border-radius: .5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .6); }
        .dt-lb-btn { position: absolute; display: flex; align-items: center; justify-content: center; width: 2.75rem; height: 2.75rem; border-radius: 9999px; color: #fff; background: rgba(255, 255, 255, .12); border: 1px solid rgba(255, 255, 255, .25); transition: background-color .2s ease; }
        .dt-lb-btn:hover { background: #ed1c24; border-color: #ed1c24; }

        /* Kontak */
        .lift { transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease; }
        .lift:hover { transform: translateY(-2px); box-shadow: 0 8px 20px -8px rgba(15, 23, 42, .18); border-color: #cbd5e1; }
        .contact-icon { transition: background-color .2s ease, color .2s ease; }
        .lift:hover .contact-icon { background: #ed1c24; color: #fff; }

        @media (prefers-reduced-motion: reduce) {
            .dt-media img, .dt-zoom, .lift, .contact-icon, .dt-lb-btn { transition: none; }
            .dt-media:hover img, .lift:hover { transform: none; }
        }
    </style>
</head>
<body class="bg-white font-sans text-slate-900 antialiased selection:bg-[#ed1c24] selection:text-white"
      x-data="{
          open: false,
          i: 0,
          items: @js($galleryItems),
          show(n) { this.i = n; this.open = true },
          next() { if (this.open) this.i = (this.i + 1) % this.items.length },
          prev() { if (this.open) this.i = (this.i - 1 + this.items.length) % this.items.length }
      }"
      x-effect="document.body.style.overflow = open ? 'hidden' : ''"
      @keydown.escape.window="open = false"
      @keydown.arrow-right.window="next()"
      @keydown.arrow-left.window="prev()">

    <!-- Navbar -->
    <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/95 backdrop-blur">
        <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:h-20 sm:px-6 lg:px-8">
            <a href="{{ url('/') }}" class="flex items-center gap-3">
                <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-9 w-auto object-contain sm:h-10">
                <span class="border-l border-slate-300 pl-3 text-xs font-bold uppercase tracking-wider text-slate-800">Asrama Kampus</span>
            </a>
            @auth
                @if(auth()->user()->role === 'penghuni')
                    <a href="{{ route('dashboard') }}" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#ed1c24] hover:text-[#ed1c24] sm:px-4 sm:text-sm">Kembali ke dashboard</a>
                @else
                    <a href="{{ url('/') }}#tentang-asrama" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#ed1c24] hover:text-[#ed1c24] sm:px-4 sm:text-sm">Kembali</a>
                @endif
            @else
                <a href="{{ url('/') }}#tentang-asrama" class="rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-[#ed1c24] hover:text-[#ed1c24] sm:px-4 sm:text-sm">Kembali</a>
            @endauth
        </div>
    </header>

    <!-- Hero -->
    <section class="relative overflow-hidden bg-slate-900 text-white" style="min-height: 17rem;">
        <div class="mx-auto flex max-w-7xl flex-col justify-center px-4 py-14 sm:px-6 sm:py-20 lg:px-8" style="min-height: 17rem;">
            <nav class="text-xs text-slate-300" aria-label="Breadcrumb">
                <a href="{{ url('/') }}" class="transition hover:text-white">Beranda</a>
                <span class="mx-2 text-slate-500">/</span>
                <span class="text-white">Detail Asrama</span>
            </nav>
            <h1 class="mt-4 text-3xl font-extrabold tracking-tight sm:text-4xl lg:text-5xl">Detail <span class="text-[#ed1c24]">Asrama</span></h1>
            <p class="mt-3 max-w-xl text-sm leading-6 text-slate-200 sm:text-base">Lihat foto fasilitas yang diunggah pengelola. Klik foto untuk memperbesar.</p>
        </div>
    </section>

    @if(!empty($cabangSummaries))
        <section class="border-b border-slate-200 bg-slate-50 py-10 sm:py-12">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#ed1c24]">Titik lokasi</p>
                <h2 class="mt-2 text-2xl font-bold text-slate-900">Cabang Asrama INSTIKI</h2>
                <div class="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach($cabangSummaries as $branch)
                        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                            <span class="font-mono text-xs text-slate-400">{{ $branch['kode'] }}</span>
                            <h3 class="mt-1 font-bold text-slate-900">{{ $branch['nama'] }}</h3>
                            <p class="mt-2 text-sm text-slate-600">{{ $branch['alamat'] }}</p>
                            <p class="mt-3 text-xs text-slate-700">{{ $branch['schemaReady'] ? $branch['kapasitasTersedia'] : '—' }} tempat tersedia dari {{ $branch['totalKapasitas'] }}</p>
                            @if($branch['maps_url'])<a class="mt-3 inline-block text-sm font-semibold text-blue-700" href="{{ $branch['maps_url'] }}" target="_blank" rel="noopener noreferrer">Lihat peta ↗</a>@endif
                            @if($branch['telepon'] || $branch['whatsapp'])<p class="mt-2 text-xs text-slate-600">@if($branch['telepon']){{ $branch['telepon'] }}@endif @if($branch['whatsapp'])· WhatsApp {{ $branch['whatsapp'] }}@endif</p>@endif
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- Fasilitas -->
    <main class="mx-auto max-w-6xl px-4 py-14 sm:px-6 sm:py-20 lg:px-8">
        @if($facilities->isNotEmpty())
            <div class="dt-list">
                @foreach($facilities as $facility)
                    <article class="dt-row grid items-center gap-6 lg:grid-cols-2 lg:gap-14">

                        {{-- Foto --}}
                        <button type="button" @click="show({{ $loop->index }})"
                                class="dt-media group relative block w-full overflow-hidden rounded-xl bg-slate-200 text-left shadow-sm"
                                aria-label="Perbesar foto {{ $facility->judul }}">
                            <img src="{{ '/storage/' . ltrim($facility->gambar_path, '/') }}" alt="{{ $facility->judul }}" class="block w-full object-cover" style="aspect-ratio: 4 / 3;">
                            <span class="dt-zoom absolute bottom-3 right-3 inline-flex items-center gap-1.5 rounded-full border border-white/40 bg-slate-950/60 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 8v6M8 11h6M19 11a8 8 0 11-16 0 8 8 0 0116 0z"/></svg>
                                Perbesar
                            </span>
                        </button>

                        {{-- Teks --}}
                        <div>
                            <div class="flex items-center gap-3">
                                <span class="text-xs font-bold tracking-[0.2em] text-[#ed1c24]">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="h-px w-12 bg-slate-300"></span>
                            </div>
                            <h2 class="mt-4 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">{{ $facility->judul }}</h2>
                            <p class="mt-3 text-sm font-semibold leading-6 text-slate-700 sm:text-base">{{ $facility->caption }}</p>
                            @if($facility->deskripsi)
                                <p class="mt-5 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $facility->deskripsi }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="mx-auto max-w-lg rounded-xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
                <h2 class="font-semibold text-slate-800">Informasi fasilitas sedang disiapkan</h2>
                <p class="mt-2 text-sm text-slate-500">Foto dan detail fasilitas akan ditampilkan di sini setelah ditambahkan oleh Admin Asrama.</p>
            </div>
        @endif
    </main>

    <!-- Kontak -->
    @if($hasContact)
        <section class="border-t border-slate-200 bg-slate-50 py-14 sm:py-16">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                <div class="max-w-xl">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-[#ed1c24]">Hubungi kami</span>
                    <h2 class="mt-2 text-2xl font-bold text-slate-900">Informasi kontak</h2>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Tanyakan fasilitas, proses reservasi, atau informasi lainnya langsung kepada pengelola asrama.</p>
                </div>

                <div class="mt-8 grid gap-3 sm:grid-cols-2">

                    @if($contact->whatsapp)
                        <a href="{{ $whatsappNumber ? 'https://wa.me/' . $whatsappNumber : '#' }}" target="_blank" rel="noopener noreferrer" class="lift flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4">
                            <span class="contact-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs text-slate-500">WhatsApp</span>
                                <span class="block truncate text-sm font-semibold text-slate-800">{{ $contact->whatsapp }}</span>
                            </span>
                        </a>
                    @endif

                    @if($contact->telepon)
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', $contact->telepon) }}" class="lift flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4">
                            <span class="contact-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs text-slate-500">Telepon</span>
                                <span class="block truncate text-sm font-semibold text-slate-800">{{ $contact->telepon }}</span>
                            </span>
                        </a>
                    @endif

                    @if($contact->email)
                        <a href="mailto:{{ $contact->email }}" class="lift flex items-center gap-3 rounded-lg border border-slate-200 bg-white p-4">
                            <span class="contact-icon flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            </span>
                            <span class="min-w-0">
                                <span class="block text-xs text-slate-500">Email</span>
                                <span class="block break-all text-sm font-semibold text-slate-800">{{ $contact->email }}</span>
                            </span>
                        </a>
                    @endif

                    @if($contact->alamat)
                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($contact->alamat) }}" target="_blank" rel="noopener noreferrer" class="lift flex items-start gap-3 rounded-lg border border-slate-200 bg-white p-4 sm:col-span-2">
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
            </div>
        </section>
    @endif

    <!-- Footer -->
    <footer class="border-t border-slate-200 bg-white py-6 text-center text-xs text-slate-500">
        &copy; {{ date('Y') }} Institut Bisnis dan Teknologi Indonesia (INSTIKI)
    </footer>

    <!-- Lightbox: perbesar foto -->
    <div x-cloak x-show="open" x-transition.opacity class="dt-lb" role="dialog" aria-modal="true" aria-label="Foto diperbesar" @click.self="open = false">

        <button type="button" class="dt-lb-btn" style="top: 1rem; right: 1rem;" @click="open = false" aria-label="Tutup">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
        </button>

        <button type="button" class="dt-lb-btn" style="left: 1rem; top: 50%; margin-top: -1.375rem;" x-show="items.length > 1" @click="prev()" aria-label="Foto sebelumnya">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
        </button>

        <button type="button" class="dt-lb-btn" style="right: 1rem; top: 50%; margin-top: -1.375rem;" x-show="items.length > 1" @click="next()" aria-label="Foto berikutnya">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
        </button>

        <template x-if="items[i]">
            <figure class="flex flex-col items-center text-center">
                <img :src="items[i].src" :alt="items[i].title" class="dt-lb-img">
                <figcaption class="mt-4 max-w-xl px-4 text-white">
                    <p class="text-base font-semibold" x-text="items[i].title"></p>
                    <p class="mt-1 text-sm text-slate-300" x-text="items[i].caption"></p>
                    <p class="mt-2 text-xs tracking-widest text-slate-400" x-show="items.length > 1" x-text="(i + 1) + ' / ' + items.length"></p>
                </figcaption>
            </figure>
        </template>
    </div>
</body>
</html>
