<x-layouts.keuangan
    title="Dashboard Keuangan - Asrama INSTIKI"
    activeMenu="dashboard">

    @php
        $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
        $paymentStatusLabels = [
            'pending'  => ['Menunggu', 'bg-amber-100 text-amber-800'],
            'paid'     => ['Lunas', 'bg-emerald-100 text-emerald-800'],
            'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
    @endphp

    <!-- Heading -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Ringkasan Keuangan</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Ikhtisar finansial dan verifikasi pembayaran sewa asrama INSTIKI.</p>
    </div>

    <!-- Grid Status Cards (4 Cards) -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Menunggu Verifikasi -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-amber-500 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Menunggu Verifikasi</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $pendingPayments }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-amber-600">Perlu tindakan verifikasi</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <!-- Pembayaran Lunas -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-emerald-500 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Pembayaran Lunas</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $paidCount }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-emerald-600">Lunas terverifikasi</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <!-- Pembayaran Ditolak -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-rose-500 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Pembayaran Ditolak</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $rejectedCount }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-rose-600">Ditolak / dikembalikan</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-300 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Pendapatan</p>
                <h3 class="mt-1 text-xl font-bold text-slate-900">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-slate-400">Total dana masuk</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Grafik Tren & Komposisi Status -->
    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">

        <!-- Tren Pendapatan -->
        <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Tren Pendapatan Tahun {{ date('Y') }}</h2>
                    <p class="text-xs text-slate-500">Berdasarkan transaksi lunas per bulan.</p>
                </div>
            </div>

            <div class="flex h-48 items-end justify-between gap-2 border-b border-slate-100 px-2 pt-8">
                @foreach($chartData as $bulanIndex => $total)
                    @php $heightPercent = $total > 0 ? round(($total / $maxAmount) * 100) : 4; @endphp
                    <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-2">
                        <div class="pointer-events-none absolute -top-8 z-20 whitespace-nowrap rounded bg-slate-800 px-2 py-1 font-mono text-[10px] text-white opacity-0 transition group-hover:opacity-100">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                        <div class="w-full rounded-t-md transition-all duration-300 {{ $total > 0 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-100' }}"
                             style="height: {{ $heightPercent }}%;"></div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between px-2 font-mono text-[11px] text-slate-400">
                @foreach($namaBulan as $m)
                    <span class="flex-1 text-center">{{ $m }}</span>
                @endforeach
            </div>
        </div>

        <!-- Komposisi Transaksi -->
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-4 text-base font-bold text-slate-900">Komposisi Transaksi</h2>

            <div class="flex items-center justify-center py-2">
                @php
                    $degPaid    = round(($pctPaid / 100) * 360);
                    $degPending = round(($pctPending / 100) * 360) + $degPaid;
                @endphp
                <div class="relative flex h-36 w-36 items-center justify-center rounded-full shadow-inner"
                     style="background: conic-gradient(#10b981 0deg {{ $degPaid }}deg, #f59e0b {{ $degPaid }}deg {{ $degPending }}deg, #f43f5e {{ $degPending }}deg 360deg);">
                    <div class="flex h-24 w-24 flex-col items-center justify-center rounded-full bg-white shadow-sm">
                        <span class="text-sm font-bold text-slate-800">{{ $totalTransaksi }}</span>
                        <span class="text-[9px] text-slate-400">Total Transaksi</span>
                    </div>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <span class="flex items-center text-slate-600"><span class="mr-2 h-3 w-3 rounded-full bg-emerald-500"></span> Lunas ({{ $pctPaid }}%)</span>
                    <span class="font-bold text-slate-800">{{ $paidCount }}</span>
                </div>
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <span class="flex items-center text-slate-600"><span class="mr-2 h-3 w-3 rounded-full bg-amber-500"></span> Menunggu ({{ $pctPending }}%)</span>
                    <span class="font-bold text-slate-800">{{ $pendingPayments }}</span>
                </div>
                <div class="flex items-center justify-between text-xs sm:text-sm">
                    <span class="flex items-center text-slate-600"><span class="mr-2 h-3 w-3 rounded-full bg-rose-500"></span> Ditolak ({{ $pctRejected }}%)</span>
                    <span class="font-bold text-slate-800">{{ $rejectedCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900">Transaksi Terbaru</h2>
                <p class="text-xs text-slate-500">Menampilkan transaksi pembayaran terakhir.</p>
            </div>
            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $pendingPayments > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $pendingPayments }} Menunggu</span>
        </div>

        @if($recentPayments->isNotEmpty())
            <div class="overflow-x-auto">
                <table class="w-full min-w-[720px] text-left text-xs text-slate-600 sm:text-sm">
                    <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-700">
                        <tr>
                            <th class="px-4 py-3.5">Kode</th>
                            <th class="px-4 py-3.5">Penghuni</th>
                            <th class="px-4 py-3.5">Metode</th>
                            <th class="px-4 py-3.5">Nominal</th>
                            <th class="px-4 py-3.5">Status</th>
                            <th class="px-4 py-3.5">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentPayments as $payment)
                            @php $label = $paymentStatusLabels[$payment->status] ?? [ucfirst($payment->status), 'bg-slate-100 text-slate-600']; @endphp
                            <tr class="transition hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3.5 font-mono font-bold text-slate-800">{{ $payment->kode_pembayaran }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="block font-bold text-slate-900">{{ $payment->user->nama ?? '-' }}</span>
                                    <span class="block text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, H:i') }}</span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3.5">{{ $payment->metode_pembayaran }}</td>
                                <td class="whitespace-nowrap px-4 py-3.5 font-bold text-slate-900">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>
                                <td class="px-4 py-3.5">
                                    <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $label[1] }}">{{ $label[0] }}</span>
                                </td>
                                <td class="px-4 py-3.5">
                                    @if($payment->status === 'paid')
                                        <a href="{{ route('keuangan.pembayaran.kuitansi', $payment->id) }}" target="_blank"
                                           class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                            <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Cetak
                                        </a>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="text-right">
                <a href="{{ route('keuangan.pembayaran.index') }}" class="text-xs font-semibold text-[#ed1c24] hover:text-red-700">Lihat semua pembayaran →</a>
            </div>
        @else
            <div class="py-12 text-center text-sm text-slate-400">
                <svg class="mx-auto mb-3 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Belum ada riwayat transaksi pembayaran.
            </div>
        @endif
    </div>

</x-layouts.keuangan>