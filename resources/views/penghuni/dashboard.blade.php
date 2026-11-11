<x-layouts.penghuni title="Dashboard Penghuni - Asrama INSTIKI" activeMenu="dashboard">

    @php
        $reservasiLabels = [
            'pending'           => ['Menunggu Admin Asrama', 'bg-amber-100 text-amber-800'],
            'approved'          => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
            'legacy_unverified' => ['Perlu diverifikasi', 'bg-orange-100 text-orange-800'],
            'rejected'          => ['Ditolak', 'bg-red-100 text-red-800'],
            'cancelled'         => ['Dibatalkan', 'bg-slate-100 text-slate-600'],
            'no_show'           => ['Tidak hadir', 'bg-slate-100 text-slate-600'],
            'completed'         => ['Selesai', 'bg-blue-100 text-blue-800'],
        ];
        $pembayaranLabels = [
            'pending'  => ['Menunggu', 'bg-amber-100 text-amber-800'],
            'paid'     => ['Lunas', 'bg-emerald-100 text-emerald-800'],
            'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        $months = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $resLabel = $reservasi
            ? ($reservasiLabels[$reservasi->status] ?? [ucfirst($reservasi->status), 'bg-slate-100 text-slate-600'])
            : null;
        $payLabel = $pembayaranTerakhir
            ? ($pembayaranLabels[$pembayaranTerakhir->status] ?? [ucfirst($pembayaranTerakhir->status), 'bg-slate-100 text-slate-600'])
            : null;
    @endphp

    <!-- Heading -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Halo, {{ auth()->user()->nama }}</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Kelola pengajuan, pantau status pembayaran, dan lihat informasi masa tinggal Anda.</p>
    </div>

    <!-- Kartu Ringkasan -->
    <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
        <!-- Status Reservasi -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-300 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Status Reservasi</p>
                @if($resLabel)
                    <span class="mt-2 inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $resLabel[1] }}">{{ $resLabel[0] }}</span>
                    <span class="mt-2 block font-mono text-[11px] text-slate-400">{{ $reservasi->kode_reservasi }}</span>
                @else
                    <h3 class="mt-1 text-xl font-bold text-slate-900">Belum ada</h3>
                    <span class="mt-2 block text-[11px] font-medium text-slate-400">Silakan ajukan reservasi.</span>
                @endif
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Masa Tinggal -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 {{ $activePlacement ? 'border-t-emerald-500' : 'border-t-amber-500' }} bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Masa Tinggal</p>
                <h3 class="mt-1 text-xl font-bold text-slate-900">{{ $activePlacement ? 'Sedang aktif' : ($reservasi?->status === 'approved' ? 'Menunggu check-in' : 'Belum aktif') }}</h3>
                <span class="mt-2 block text-[11px] font-medium {{ $activePlacement ? 'text-emerald-600' : 'text-slate-400' }}">
                    @if($activePlacement)
                        {{ $activePlacement->tanggal_mulai->format('d M Y') }} – {{ $activePlacement->tanggal_akhir->format('d M Y') }}
                    @else
                        Data kamar ditampilkan kepada petugas asrama.
                    @endif
                </span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $activePlacement ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <!-- Pembayaran Terakhir -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-300 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Pembayaran Terakhir</p>
                @if($payLabel)
                    <span class="mt-2 inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $payLabel[1] }}">{{ $payLabel[0] }}</span>
                    <span class="mt-2 block font-mono text-[11px] text-slate-400">{{ $pembayaranTerakhir->kode_pembayaran }}</span>
                    <span class="mt-1 block text-xs font-semibold text-slate-700">Rp {{ number_format($pembayaranTerakhir->jumlah_bayar, 0, ',', '.') }}</span>
                    @if($pembayaranTerakhir->reservasi?->durasi_bulan)
                        <span class="block text-[11px] text-slate-500">{{ $pembayaranTerakhir->reservasi->durasi_bulan }} bulan · {{ $pembayaranTerakhir->reservasi->tanggal_mulai_disetujui?->format('d M Y') }} – {{ $pembayaranTerakhir->reservasi->tanggal_akhir_disetujui?->format('d M Y') }}</span>
                    @endif
                @else
                    <h3 class="mt-1 text-xl font-bold text-slate-900">Belum ada</h3>
                    <span class="mt-2 block text-[11px] font-medium text-slate-400">Belum ada transaksi pembayaran.</span>
                @endif
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
        </div>
    </div>

    <section class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="grid gap-5 p-5 sm:p-6 md:grid-cols-[minmax(0,1fr)_auto] md:items-center">
            <div class="flex items-start gap-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-[#ed1c24]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5M5 9v11h14V9M9 20v-6h6v6" />
                    </svg>
                </span>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-[#ed1c24]">Kenali lingkungan hunian</p>
                    <h2 class="mt-1 text-base font-bold text-slate-900 sm:text-lg">Informasi Asrama INSTIKI</h2>
                    <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-600">Lihat galeri fasilitas, deskripsi, dan kontak pengelola pada halaman detail asrama.</p>
                </div>
            </div>
            <a href="{{ route('asrama.detail') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700 focus:outline-none focus:ring-2 focus:ring-slate-400 focus:ring-offset-2">
                Lihat detail asrama
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6" /></svg>
            </a>
        </div>
    </section>

    @if($assignedPlacements->isNotEmpty())
        <section class="space-y-3 rounded-lg border border-blue-100 bg-white p-5 shadow-sm">
            <div>
                <h2 class="text-base font-bold text-slate-900">Informasi kamar dan masa tinggal</h2>
                <p class="mt-1 text-xs text-slate-500">Hanya penempatan milik Anda yang ditampilkan.</p>
            </div>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                @foreach($assignedPlacements as $placement)
                    <article class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <strong class="text-sm text-slate-900">Kamar {{ $placement->kamar?->nomor_kamar ?? 'menunggu alokasi' }}</strong>
                            <span class="rounded-full px-2.5 py-1 text-[10px] font-bold {{ $placement->status === 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $placement->status === 'active' ? 'Aktif' : 'Menunggu check-in' }}
                            </span>
                        </div>
                        @if($placement->kamar)
                            <p class="mt-1 text-xs text-slate-600">Blok {{ $placement->kamar->blok }}</p>
                        @endif
                        <p class="mt-1 text-xs font-semibold text-blue-800">Cabang: {{ $placement->cabangAsrama?->nama ?? 'Belum tercatat pada placement ini' }}</p>
                        @if($placement->cabangAsrama?->alamat)
                            <p class="mt-1 text-xs text-slate-500">{{ $placement->cabangAsrama->alamat }}</p>
                        @endif
                        <p class="mt-2 text-xs text-slate-600">
                            {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}
                            <span class="text-slate-400">(akhir tidak termasuk)</span>
                        </p>
                    </article>
                @endforeach
            </div>
        </section>
    @elseif($reservasi?->status === 'pending')
        <section class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
            Pengajuan {{ $reservasi->durasi_bulan ?? '—' }} bulan menunggu penetapan tanggal mulai dan alokasi kamar oleh Admin Asrama.
        </section>
    @endif

    <!-- Aksi Cepat -->
    <div class="flex flex-wrap gap-3">
        @if(!$hasOpenReservation)
            <a href="{{ route('penghuni.reservasi.index') }}" class="rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Ajukan reservasi</a>
        @endif
        @if($reservasi?->status === 'approved')
            <a href="{{ route('penghuni.pembayaran.index') }}" class="rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Pembayaran</a>
        @endif
        <a href="{{ route('penghuni.kamar.index') }}" class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Kapasitas asrama</a>
    </div>

    <!-- Grafik Pembayaran -->
    <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pembayaran Lunas per Bulan</h2>
                <p class="text-xs text-slate-500">Tahun {{ now()->year }}.</p>
            </div>
            <a href="{{ route('penghuni.pembayaran.index') }}" class="text-xs font-semibold text-[#ed1c24] hover:text-red-700">Lihat riwayat →</a>
        </div>

        <div class="flex h-48 items-end justify-between gap-2 border-b border-slate-100 px-2 pt-8">
            @foreach($chartData as $month => $amount)
                <div class="group relative flex h-full flex-1 flex-col items-center justify-end">
                    <div class="pointer-events-none absolute -top-8 z-20 whitespace-nowrap rounded bg-slate-800 px-2 py-1 font-mono text-[10px] text-white opacity-0 transition group-hover:opacity-100">
                        Rp {{ number_format($amount, 0, ',', '.') }}
                    </div>
                    <div class="w-full rounded-t-md transition-all duration-300 {{ $amount > 0 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-100' }}"
                         style="height: {{ $amount > 0 ? max(8, round(($amount / $maxAmount) * 100)) : 3 }}%;"></div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between px-2 font-mono text-[11px] text-slate-400">
            @foreach($chartData as $month => $amount)
                <span class="flex-1 text-center">{{ $months[$month] }}</span>
            @endforeach
        </div>
    </div>

</x-layouts.penghuni>
