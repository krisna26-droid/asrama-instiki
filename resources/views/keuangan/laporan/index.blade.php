<x-layouts.keuangan
    title="Laporan Keuangan - Admin Keuangan INSTIKI"
    activeMenu="laporan">

    @php
        $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $laporanCards = [
            ['slug' => 'pendapatan', 'title' => 'Laporan Pendapatan', 'desc' => 'Rincian pendapatan sewa bulanan terverifikasi lunas', 'accent' => 'emerald',
             'icon' => 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6'],
            ['slug' => 'log-pembayaran', 'title' => 'Log Pembayaran', 'desc' => 'Seluruh rekam jejak transaksi pembayaran pengguna', 'accent' => 'blue',
             'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z'],
            ['slug' => 'tunggakan', 'title' => 'Laporan Menunggu Verifikasi', 'desc' => 'Daftar transaksi pembayaran yang perlu diverifikasi', 'accent' => 'amber',
             'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['slug' => 'penolakan', 'title' => 'Laporan Penolakan', 'desc' => 'Analisis dan catatan bukti pembayaran yang ditolak', 'accent' => 'rose',
             'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['slug' => 'metode-pembayaran', 'title' => 'Rincian Metode Pembayaran', 'desc' => 'Distribusi penggunaan kanal pembayaran', 'accent' => 'purple',
             'icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z'],
            ['slug' => 'ringkasan-tahunan', 'title' => 'Ringkasan Tahunan', 'desc' => 'Rekapitulasi keuangan tahunan secara menyeluruh', 'accent' => 'slate',
             'icon' => 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
        ];

        // Kelas ditulis penuh agar terdeteksi Tailwind
        $accentClasses = [
            'emerald' => ['border-t-emerald-500', 'bg-emerald-50 text-emerald-600'],
            'blue'    => ['border-t-blue-500',    'bg-blue-50 text-blue-600'],
            'amber'   => ['border-t-amber-500',   'bg-amber-50 text-amber-600'],
            'rose'    => ['border-t-rose-500',    'bg-rose-50 text-rose-600'],
            'purple'  => ['border-t-purple-500',  'bg-purple-50 text-purple-600'],
            'slate'   => ['border-t-slate-300',   'bg-slate-100 text-slate-600'],
        ];
    @endphp

    <!-- Heading -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Laporan Keuangan</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Laporan operasional keuangan dan ekspor rekapitulasi pembayaran.</p>
    </div>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    <!-- Grid Kartu Laporan (6 Kartu Ekspor) -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($laporanCards as $card)
            @php [$borderClass, $iconClass] = $accentClasses[$card['accent']]; @endphp
            <div class="flex flex-col justify-between rounded-lg border border-slate-200 border-t-2 {{ $borderClass }} bg-white p-5 shadow-sm">
                <div>
                    <div class="mb-4 flex items-center justify-between">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $iconClass }}">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"/></svg>
                        </div>
                        <a href="{{ route('keuangan.laporan.export', $card['slug']) }}" class="inline-flex items-center rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                            Ekspor CSV
                        </a>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">{{ $card['title'] }}</h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $card['desc'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Grafik Bulanan (Pendapatan vs Menunggu Verifikasi) -->
    <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col justify-between gap-2 border-b border-slate-100 pb-4 sm:flex-row sm:items-center">
            <div>
                <h2 class="text-base font-bold text-slate-900">Pendapatan Lunas vs Menunggu Verifikasi</h2>
                <p class="text-xs text-slate-500">Perbandingan per bulan, tahun {{ date('Y') }}.</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-medium">
                <span class="inline-flex items-center gap-1.5 text-slate-600">
                    <span class="h-3 w-3 rounded-full bg-emerald-500"></span> Pendapatan Lunas
                </span>
                <span class="inline-flex items-center gap-1.5 text-slate-600">
                    <span class="h-3 w-3 rounded-full bg-amber-500"></span> Menunggu Verifikasi
                </span>
            </div>
        </div>

        <div class="flex h-64 items-end justify-between gap-2 border-b border-slate-100 px-2 pt-8">
            @foreach($namaBulan as $bulanIdx => $label)
                @php
                    $valPaid    = $chartPaid[$bulanIdx] ?? 0;
                    $valPending = $chartPending[$bulanIdx] ?? 0;
                    $hPaid      = $valPaid > 0 ? round(($valPaid / $maxAmount) * 100) : 3;
                    $hPending   = $valPending > 0 ? round(($valPending / $maxAmount) * 100) : 3;
                @endphp
                <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-1">
                    <div class="pointer-events-none absolute -top-10 z-20 whitespace-nowrap rounded bg-slate-800 px-2 py-1 font-mono text-[10px] text-white opacity-0 shadow-md transition group-hover:opacity-100">
                        Lunas: Rp {{ number_format($valPaid, 0, ',', '.') }} | Pending: Rp {{ number_format($valPending, 0, ',', '.') }}
                    </div>

                    <div class="flex h-full w-full items-end justify-center gap-1">
                        <div class="w-1/2 rounded-t transition-all duration-300 {{ $valPaid > 0 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-100' }}" style="height: {{ $hPaid }}%;"></div>
                        <div class="w-1/2 rounded-t transition-all duration-300 {{ $valPending > 0 ? 'bg-amber-500 hover:bg-amber-600' : 'bg-slate-100' }}" style="height: {{ $hPending }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex items-center justify-between px-2 font-mono text-[11px] text-slate-400">
            @foreach($namaBulan as $m)
                <span class="flex-1 text-center">{{ $m }}</span>
            @endforeach
        </div>
    </div>

</x-layouts.keuangan>