<x-layouts.penghuni title="Ketersediaan Asrama - Asrama INSTIKI" activeMenu="kamar">

    <!-- Heading -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">Kapasitas Asrama</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Informasi ketersediaan umum. Sistem tidak menampilkan nomor kamar dan penghuni tidak memilih kamar tertentu.</p>
    </header>

    <!-- Kartu Statistik -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-300 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Kapasitas Aktif</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ number_format($capacity['totalKapasitas']) }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-slate-400">orang</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 5h5"></path></svg>
            </div>
        </div>

        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-700 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Penghuni Aktif</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $capacity['schemaReady'] ? number_format($capacity['penghuniAktif']) : '—' }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-slate-400">Sudah menempati</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-emerald-500 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Kapasitas Tersedia</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $capacity['schemaReady'] ? number_format($capacity['kapasitasTersedia']) : '—' }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-emerald-600">Siap ditempati</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-amber-500 bg-white p-5 shadow-sm">
            <div class="min-w-0 flex-1 pr-3">
                <p class="text-xs font-semibold text-slate-500">Keterisian</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $capacity['schemaReady'] ? $capacity['persentase'].'%' : '—' }}</h3>
                @if($capacity['schemaReady'])
                    <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-slate-100">
                        <div class="h-full rounded-full bg-amber-500" style="width: {{ min(100, max(0, (int) $capacity['persentase'])) }}%;"></div>
                    </div>
                @endif
                <span class="mt-2 block text-[11px] font-medium text-amber-600">{{ $capacity['statusKetersediaan'] }}</span>
            </div>
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Proses Penempatan -->
    <div class="space-y-3 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Proses Penempatan</h2>
        </div>
        <p class="text-sm leading-relaxed text-slate-600">Ketersediaan menunjukkan kapasitas yang belum terikat pada hari ini. Penempatan yang telah disetujui masuk hitungan; pengajuan pending tidak mengunci slot. Ajukan reservasi dengan KTP, lalu Admin Asrama menetapkan periode dan sistem mencocokkan kategori identitas yang telah diverifikasi.</p>

        @if($hasActiveRequest)
            <p class="rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800">Anda sudah mempunyai reservasi yang masih terbuka.</p>
        @else
            <a href="{{ route('penghuni.reservasi.index') }}" class="inline-flex rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Ajukan reservasi</a>
        @endif
    </div>

</x-layouts.penghuni>