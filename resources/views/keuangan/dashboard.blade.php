<x-layouts.keuangan 
    title="Dashboard Keuangan - Asrama INSTIKI" 
    activeMenu="dashboard">

    <!-- Title & Description -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Dashboard</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Financial overview of dormitory payments</p>
    </div>

    <!-- Top 4 Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Pending Payments -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Pending Payments</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $pendingPayments }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center border border-amber-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4">Awaiting verification</p>
        </div>

        <!-- Card 2: Paid -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Paid</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $paidCount }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center border border-emerald-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-emerald-600 mt-4 inline-flex items-center gap-1">
                Lunas Terverifikasi
            </p>
        </div>

        <!-- Card 3: Rejected -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Rejected</p>
                    <h3 class="text-2xl font-bold text-slate-900 mt-2">{{ $rejectedCount }}</h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center border border-rose-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4">Ditolak</p>
        </div>

        <!-- Card 4: Total Revenue -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Total Revenue</p>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center border border-blue-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-emerald-600 mt-4 inline-flex items-center gap-1">
                Total Diterima
            </p>
        </div>

    </div>

    <!-- Charts / Trend Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Revenue Trend (12 Months) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <h2 class="text-xs font-bold text-slate-700">Revenue Trend (12 months)</h2>
            <div class="h-56 flex items-end justify-between border-b border-slate-100 pb-2 text-[11px] text-slate-400 mt-8">
                <span>Jan</span>
                <span>Feb</span>
                <span>Mar</span>
                <span>Apr</span>
                <span>May</span>
                <span>Jun</span>
                <span>Jul</span>
                <span>Aug</span>
                <span>Sep</span>
                <span>Oct</span>
                <span>Nov</span>
                <span>Dec</span>
            </div>
        </div>

        <!-- Payment Status (Donut Visual) -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-6">
            <h2 class="text-xs font-bold text-slate-700">Payment Status</h2>
            
            <div class="flex items-center justify-center py-4">
                <div class="relative w-36 h-36 rounded-full border-[12px] border-emerald-500 border-t-amber-400 border-r-rose-500 flex items-center justify-center">
                    <span class="text-xs font-bold text-slate-700">Total {{ $paidCount + $pendingPayments + $rejectedCount }}</span>
                </div>
            </div>

            <div class="space-y-2.5 text-xs">
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 mr-2"></span> Paid
                    </span>
                    <span class="font-bold text-slate-800">{{ $paidCount }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400 mr-2"></span> Pending
                    </span>
                    <span class="font-bold text-slate-800">{{ $pendingPayments }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="flex items-center text-slate-600">
                        <span class="w-2.5 h-2.5 rounded-full bg-rose-500 mr-2"></span> Rejected
                    </span>
                    <span class="font-bold text-slate-800">{{ $rejectedCount }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Payments Section -->
    <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">Recent Payments</h2>
            <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-full">Live</span>
        </div>

        <div class="space-y-3">
            @forelse($recentPayments as $payment)
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-slate-900">{{ $payment->user->nama ?? '-' }}</h3>
                            <p class="text-[11px] text-slate-400">
                                {{ \Carbon\Carbon::parse($payment->created_at)->format('F Y') }} • {{ $payment->metode_pembayaran }}
                            </p>
                        </div>
                    </div>
                    <div class="text-right flex items-center space-x-4">
                        <span class="text-xs font-bold text-slate-900">
                            Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}
                        </span>
                        
                        @if($payment->status === 'pending')
                            <span class="px-3 py-1 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full">• Pending</span>
                        @elseif($payment->status === 'paid')
                            <span class="px-3 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full">• Lunas</span>
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