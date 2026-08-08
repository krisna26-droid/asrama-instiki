<x-layouts.keuangan 
    title="Dashboard Keuangan - Asrama INSTIKI" 
    activeMenu="dashboard">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Dashboard Keuangan</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Ikhtisar finansial dan verifikasi pembayaran sewa asrama</p>
    </div>

    <!-- 4 Ringkasan Kartu Atas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Menunggu Verifikasi</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $pendingPayments }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-[11px] text-amber-600 font-semibold mt-4">Perlu tindakan verifikasi</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Pembayaran Lunas</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $paidCount }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-emerald-600 mt-4 inline-flex items-center gap-1">Lunas Terverifikasi</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Pembayaran Ditolak</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $rejectedCount }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
            </div>
            <p class="text-[11px] text-rose-600 font-semibold mt-4">Ditolak / Dikembalikan</p>
        </div>

        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Total Pendapatan</p>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center border border-blue-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-emerald-600 mt-4 inline-flex items-center gap-1">Total Dana Masuk</p>
        </div>
    </div>

    <!-- Grafik Tren & Komposisi Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h2 class="text-xs font-bold text-slate-700">Tren Pendapatan Tahun {{ date('Y') }}</h2>
                <span class="text-[10px] text-slate-400 font-mono">Berdasarkan Transaksi Lunas</span>
            </div>

            <div class="h-48 flex items-end justify-between gap-2 pt-8 px-2 border-b border-slate-100">
                @php
                    $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp

                @foreach($chartData as $bulanIndex => $total)
                    @php
                        $heightPercent = $total > 0 ? round(($total / $maxAmount) * 100) : 4;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group relative">
                        <div class="opacity-0 group-hover:opacity-100 transition absolute -top-8 bg-slate-800 text-white text-[10px] py-1 px-2 rounded font-mono pointer-events-none z-20 whitespace-nowrap">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>
                        <div class="w-full rounded-t-md transition-all duration-300 {{ $total > 0 ? 'bg-emerald-500 hover:bg-emerald-600' : 'bg-slate-100' }}"
                             style="height: {{ $heightPercent }}%;">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 px-2 font-mono">
                @foreach($namaBulan as $m)
                    <span class="flex-1 text-center">{{ $m }}</span>
                @endforeach
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-700">Komposisi Transaksi</h2>
            
            <div class="flex items-center justify-center py-2">
                @php
                    $degPaid    = round(($pctPaid / 100) * 360);
                    $degPending = round(($pctPending / 100) * 360) + $degPaid;
                @endphp
                <div class="relative w-36 h-36 rounded-full flex items-center justify-center shadow-inner"
                     style="background: conic-gradient(#10b981 0deg {{ $degPaid }}deg, #f59e0b {{ $degPaid }}deg {{ $degPending }}deg, #f43f5e {{ $degPending }}deg 360deg);">
                    <div class="w-24 h-24 bg-white rounded-full flex flex-col items-center justify-center shadow-sm">
                        <span class="text-xs font-bold text-slate-800">{{ $totalTransaksi }}</span>
                        <span class="text-[9px] text-slate-400">Total Transaksi</span>
                    </div>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span> Lunas ({{ $pctPaid }}%)</span>
                    <span class="font-bold text-slate-800">{{ $paidCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 mr-2"></span> Menunggu ({{ $pctPending }}%)</span>
                    <span class="font-bold text-slate-800">{{ $pendingPayments }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600"><span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span> Ditolak ({{ $pctRejected }}%)</span>
                    <span class="font-bold text-slate-800">{{ $rejectedCount }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaksi Terbaru -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Transaksi Terbaru</h2>
            <a href="{{ route('keuangan.pembayaran.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 transition">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="space-y-3">
            @forelse($recentPayments as $payment)
                <div class="p-4 bg-slate-50 hover:bg-slate-100/80 transition rounded-xl border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">{{ $payment->user->nama ?? '-' }} <span class="text-slate-400 font-mono font-normal">({{ $payment->kode_pembayaran }})</span></h3>
                            <p class="text-[11px] text-slate-400">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('d M Y, H:i') }} • {{ $payment->metode_pembayaran }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right flex items-center space-x-3">
                        <span class="text-xs font-bold text-slate-900">
                            Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                        </span>
                        
                        @if($payment->status === 'pending')
                            <span class="px-3 py-1 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">• Menunggu</span>
                        @elseif($payment->status === 'paid')
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full">• Lunas</span>
                            <a href="{{ route('keuangan.pembayaran.kuitansi', $payment->id) }}" target="_blank"
                               class="px-2 py-1 bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition inline-flex items-center gap-1 shadow-sm">
                                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak
                            </a>
                        @else
                            <span class="px-3 py-1 text-[10px] font-bold bg-rose-100 text-rose-800 rounded-full">• Ditolak</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-8 text-center text-slate-400 text-xs">
                    Belum ada riwayat transaksi pembayaran.
                </div>
            @endforelse
        </div>
    </div>

</x-layouts.keuangan>