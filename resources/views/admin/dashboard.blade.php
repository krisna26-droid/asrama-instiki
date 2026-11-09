<x-layouts.admin
    title="Dashboard - Admin Asrama INSTIKI"
    activeMenu="dashboard">

    <!-- Heading -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Ringkasan Operasional</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Gambaran umum ketersediaan kamar, reservasi, dan aktivitas asrama INSTIKI.</p>
    </div>

    <!-- Grid Status Cards (4 Cards) -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <!-- Total Kamar -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-300 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Total Kamar</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $totalKamar }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-slate-400">Kapasitas 3 org/kamar</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m-5 0V11m0 5h5"></path></svg>
            </div>
        </div>

        <!-- Kamar Tersedia -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-emerald-500 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Kamar Tersedia</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $kamarTersedia }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-emerald-600">Siap ditempati</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>

        <!-- Kamar Penuh -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-slate-700 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Kamar Penuh</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $kamarPenuh }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-slate-400">Terisi 3 penghuni</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </div>
        </div>

        <!-- Pending Verifikasi -->
        <div class="flex items-start justify-between rounded-lg border border-slate-200 border-t-2 border-t-amber-500 bg-white p-5 shadow-sm">
            <div>
                <p class="text-xs font-semibold text-slate-500">Pending Reservasi</p>
                <h3 class="mt-1 text-2xl font-bold text-slate-900">{{ $pendingReservasi }}</h3>
                <span class="mt-2 block text-[11px] font-medium text-amber-600">Menunggu verifikasi</span>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Main Grid: Reservasi Terbaru & Activity Feed -->
    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">

        <!-- Kolom Kiri: Reservasi Terbaru -->
        <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:col-span-2">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Pengajuan Reservasi Terbaru</h2>
                    <p class="text-xs text-slate-500">Menampilkan hingga 5 reservasi terakhir.</p>
                </div>
                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $pendingReservasi > 0 ? 'bg-amber-100 text-amber-800' : 'bg-slate-100 text-slate-600' }}">{{ $pendingReservasi }} Menunggu</span>
            </div>

            @if($latestReservasi->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[640px] text-left text-xs text-slate-600 sm:text-sm">
                        <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase text-slate-700">
                            <tr>
                                <th class="px-4 py-3.5">Kode</th>
                                <th class="px-4 py-3.5">Penghuni</th>
                                <th class="px-4 py-3.5">Kamar</th>
                                <th class="px-4 py-3.5">Pengajuan</th>
                                <th class="px-4 py-3.5">Durasi</th>
                                <th class="px-4 py-3.5">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($latestReservasi as $reservasi)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="whitespace-nowrap px-4 py-3.5 font-mono font-bold text-slate-800">{{ $reservasi->kode_reservasi }}</td>
                                    <td class="px-4 py-3.5">
                                        <span class="block font-bold text-slate-900">{{ $reservasi->user->nama ?? '-' }}</span>
                                        <span class="block text-[11px] text-slate-400">{{ $reservasi->user->nik ?? 'NIK belum diverifikasi' }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">
                                        @if($reservasi->kamar)
                                            <span class="font-bold text-slate-800">{{ $reservasi->kamar->nomor_kamar }}</span>
                                            <span class="block text-[11px] text-slate-400">Blok {{ $reservasi->kamar->blok }}</span>
                                        @else
                                            <span class="font-medium italic text-amber-600">Belum ditentukan</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3.5">{{ \Carbon\Carbon::parse($reservasi->tanggal_pengajuan)->format('d M Y') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3.5 font-medium">{{ $reservasi->durasi_sewa }}</td>
                                    <td class="px-4 py-3.5">
                                        @if($reservasi->status === 'pending')
                                            <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[11px] font-bold text-amber-800">Menunggu</span>
                                        @elseif($reservasi->status === 'approved')
                                            <span class="rounded-full bg-emerald-100 px-2.5 py-1 text-[11px] font-bold text-emerald-800">Disetujui</span>
                                        @elseif($reservasi->status === 'rejected')
                                            <span class="rounded-full bg-red-100 px-2.5 py-1 text-[11px] font-bold text-red-800">Ditolak</span>
                                        @else
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-600">Dibatalkan</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    <a href="{{ route('admin.reservasi.index') }}" class="text-xs font-semibold text-[#ed1c24] hover:text-red-700">Lihat semua reservasi →</a>
                </div>
            @else
                <div class="py-12 text-center text-sm text-slate-400">
                    <svg class="mx-auto mb-3 h-12 w-12 text-slate-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Belum ada pengajuan reservasi masuk.
                </div>
            @endif
        </div>

        <!-- Kolom Kanan: Status Kamar & Log Aktivitas -->
        <div class="space-y-6">

            <!-- Distribution Card -->
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-4 text-base font-bold text-slate-900">Status Kamar</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <span class="flex items-center text-slate-600">
                            <span class="mr-2 h-3 w-3 rounded-full bg-emerald-500"></span> Kamar Tersedia
                        </span>
                        <span class="font-bold text-slate-800">{{ $kamarTersedia }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <span class="flex items-center text-slate-600">
                            <span class="mr-2 h-3 w-3 rounded-full bg-slate-700"></span> Kamar Penuh
                        </span>
                        <span class="font-bold text-slate-800">{{ $kamarPenuh }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs sm:text-sm">
                        <span class="flex items-center text-slate-600">
                            <span class="mr-2 h-3 w-3 rounded-full bg-amber-500"></span> Dalam Perbaikan
                        </span>
                        <span class="font-bold text-slate-800">{{ $kamarPerbaikan }}</span>
                    </div>
                </div>
            </div>

            <!-- Activity Feed -->
            <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="mb-4 text-base font-bold text-slate-900">Aktivitas Terkini</h2>
                <div class="space-y-4 text-xs">
                    <div class="flex items-start space-x-3">
                        <div class="mt-1.5 h-2 w-2 shrink-0 rounded-full bg-[#ed1c24]"></div>
                        <div>
                            <p class="font-bold text-slate-800">Sistem Asrama Aktif</p>
                            <p class="text-[11px] text-slate-500">Siap mengelola data kamar dan reservasi.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

</x-layouts.admin>