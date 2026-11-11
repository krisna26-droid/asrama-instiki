<x-layouts.penghuni title="Reservasi Asrama - Asrama INSTIKI" activeMenu="reservasi">

    @php
        $statusLabels = [
            'pending'           => ['Menunggu persetujuan Admin Asrama', 'bg-amber-100 text-amber-800'],
            'approved'          => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
            'legacy_unverified' => ['Reservasi lama · perlu diverifikasi', 'bg-orange-100 text-orange-800'],
            'no_show'           => ['Tidak hadir · perlu tindak lanjut Admin', 'bg-slate-100 text-slate-600'],
            'rejected'          => ['Ditolak', 'bg-red-100 text-red-800'],
            'cancelled'         => ['Dibatalkan', 'bg-slate-100 text-slate-600'],
            'completed'         => ['Selesai', 'bg-blue-100 text-blue-800'],
        ];
    @endphp

    <!-- Heading -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">Reservasi Asrama</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Ajukan tempat tinggal tanpa memilih kamar. Admin Asrama menetapkan periode dan sistem mengalokasikan kamar sesuai data identitas serta kapasitas.</p>
    </header>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800">{{ session('error') }}</div>
    @endif
    @foreach($errors->all() as $error)
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    @if($reservasiAktif)
        @php $aktifLabel = $statusLabels[$reservasiAktif->status] ?? ['Perlu ditinjau Admin Asrama', 'bg-slate-100 text-slate-600']; @endphp

        <!-- Pengajuan Aktif -->
        <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3 border-b border-slate-100 pb-4">
                <div>
                    <p class="font-mono text-[11px] text-slate-400">{{ $reservasiAktif->kode_reservasi }}</p>
                    <h2 class="mt-0.5 text-base font-bold text-slate-900">Pengajuan Anda</h2>
                </div>
                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $aktifLabel[1] }}">{{ $aktifLabel[0] }}</span>
            </div>

            <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                <div>
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Tanggal pengajuan</dt>
                    <dd class="mt-1 font-medium text-slate-800">{{ \Carbon\Carbon::parse($reservasiAktif->tanggal_pengajuan)->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Status periode</dt>
                    <dd class="mt-1 font-medium text-slate-800">
                        @if($reservasiAktif->durasi_bulan)
                            Durasi {{ $reservasiAktif->durasi_bulan }} bulan
                        @else
                            {{ $reservasiAktif->penempatans->isNotEmpty() ? 'Periode telah ditetapkan' : 'Menunggu penetapan Admin Asrama' }}
                        @endif
                    </dd>
                </div>
            </dl>

            @if($reservasiAktif->penempatans->isNotEmpty())
                <div class="space-y-2 rounded-lg border border-blue-100 bg-blue-50/60 p-4">
                    <h3 class="text-xs font-bold uppercase tracking-wide text-blue-900">Kamar dan periode penempatan</h3>
                    @foreach($reservasiAktif->penempatans->sortBy('tanggal_mulai') as $placement)
                        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-blue-100 pt-2 text-xs">
                            <span class="font-semibold text-slate-800">
                                @if($placement->kamar)
                                    Kamar {{ $placement->kamar->nomor_kamar }} · Blok {{ $placement->kamar->blok }}
                                @else
                                    Menunggu alokasi kamar
                                @endif
                            </span>
                            <span class="w-full text-[11px] font-semibold text-blue-800">Cabang: {{ $placement->cabangAsrama?->nama ?? 'Belum tercatat pada placement ini' }}</span>
                            <span class="text-slate-600">
                                {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}
                                <span class="text-slate-400">(tanggal akhir tidak termasuk)</span>
                                · {{ $placement->status === 'active' ? 'Aktif' : ($placement->status === 'scheduled' ? 'Menunggu check-in' : ucfirst($placement->status)) }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @elseif($reservasiAktif->status === 'pending')
                <p class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">
                    Pengajuan {{ $reservasiAktif->durasi_bulan ?? '—' }} bulan sedang menunggu Admin Asrama menetapkan tanggal mulai dan alokasi kamar.
                </p>
            @endif

            @if($reservasiAktif->foto_ktp_path)
                <a target="_blank" rel="noopener" href="{{ route('dokumen.ktp', $reservasiAktif) }}" class="inline-flex rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Lihat / unduh KTP yang diunggah</a>
            @endif
        </div>
    @else
        <!-- Form Pengajuan -->
        <div class="space-y-5 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-xs leading-relaxed text-slate-600 sm:text-sm">
                <strong class="text-slate-800">Alokasi kamar otomatis.</strong> Nomor kamar dan kategori tidak dapat dipilih. Sistem akan mencocokkan penempatan dengan data identitas yang dikelola Admin Asrama.
            </div>

            <form action="{{ route('penghuni.reservasi.store') }}" method="POST" enctype="multipart/form-data" class="max-w-xl space-y-5">
                @csrf
                <div>
                    <label for="durasi_bulan" class="mb-1 block text-xs font-semibold text-slate-700">Durasi tinggal</label>
                    <select id="durasi_bulan" name="durasi_bulan" required class="block w-full rounded-lg border-slate-300 text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                        @for($month = 1; $month <= 12; $month++)
                            <option value="{{ $month }}" @selected((int) old('durasi_bulan', 1) === $month)>{{ $month }} bulan</option>
                        @endfor
                    </select>
                    <p class="mt-1 text-[11px] text-slate-500">Maksimal 12 bulan. Admin menetapkan tanggal mulai; tanggal akhir dan total tagihan dihitung otomatis.</p>
                    @error('durasi_bulan')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="foto_ktp" class="mb-1 block text-xs font-semibold text-slate-700">KTP (wajib)</label>
                    <input id="foto_ktp" type="file" name="foto_ktp" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" required class="block w-full rounded-lg border border-slate-300 p-2 text-sm">
                    <p class="mt-1 text-[11px] text-slate-500">JPG, JPEG, PNG atau PDF, maksimum 5 MB.</p>
                    @error('foto_ktp')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <button class="rounded-lg bg-[#ed1c24] px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Kirim pengajuan</button>
            </form>
        </div>
    @endif

    <!-- Riwayat Reservasi -->
    <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Riwayat Reservasi</h2>
            <p class="text-xs text-slate-500">Daftar pengajuan lama tetap tersimpan untuk referensi Anda.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-left text-xs text-slate-600 sm:text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-700">
                    <tr>
                        <th class="px-4 py-3.5">Kode</th>
                        <th class="px-4 py-3.5">Tanggal Pengajuan</th>
                        <th class="px-4 py-3.5">Periode Penempatan</th>
                        <th class="px-4 py-3.5">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservasiRiwayat as $item)
                        @php $itemLabel = $statusLabels[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600']; @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3.5 font-mono font-bold text-slate-800">{{ $item->kode_reservasi }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5">{{ \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d M Y') }}</td>
                            <td class="px-4 py-3.5">
                                @forelse($item->penempatans as $placement)
                                    <span class="mb-1 block whitespace-nowrap">
                                        {{ $placement->kamar?->nomor_kamar ? 'Kamar '.$placement->kamar->nomor_kamar.' · ' : '' }}
                                        {{ $placement->cabangAsrama?->nama ?? 'Cabang belum tercatat' }} ·
                                        {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}
                                        <span class="text-[11px] text-slate-400">({{ ucfirst($placement->status) }})</span>
                                    </span>
                                @empty
                                    <span class="text-slate-400">Belum ada penempatan tercatat</span>
                                @endforelse
                            </td>
                            <td class="px-4 py-3.5">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $itemLabel[1] }}">{{ $itemLabel[0] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-12 text-center text-sm text-slate-400">Belum ada reservasi pada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($reservasiRiwayat->hasPages())
            <div class="border-t border-slate-100 pt-4">{{ $reservasiRiwayat->links() }}</div>
        @endif
    </div>

</x-layouts.penghuni>
