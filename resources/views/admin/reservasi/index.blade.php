<x-layouts.admin title="Reservasi - Admin Asrama" activeMenu="reservasi">

    @php
        $statusLabels = [
            'pending'           => ['Menunggu', 'bg-amber-100 text-amber-800'],
            'approved'          => ['Disetujui', 'bg-emerald-100 text-emerald-800'],
            'legacy_unverified' => ['Reservasi lama · belum diverifikasi', 'bg-orange-100 text-orange-800'],
            'rejected'          => ['Ditolak', 'bg-red-100 text-red-800'],
            'cancelled'         => ['Dibatalkan', 'bg-slate-100 text-slate-600'],
            'no_show'           => ['Tidak hadir', 'bg-slate-100 text-slate-600'],
            'completed'         => ['Selesai', 'bg-blue-100 text-blue-800'],
        ];
        $placementLabels = [
            'active'    => 'Aktif',
            'scheduled' => 'Terjadwal',
        ];
        $inputClass = 'w-full rounded-lg border-slate-300 text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24]';
        $labelClass = 'mb-1 block text-xs font-semibold text-slate-700';
    @endphp

    <!-- Heading -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">Reservasi</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Tinjau pengajuan dan alokasikan kamar otomatis berdasarkan kategori penghuni serta kapasitas interval.</p>
    </header>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @foreach($errors->all() as $error)
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.reservasi.index') }}" class="grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-7">
        <div class="lg:col-span-2">
            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Pencarian</label>
            <input name="search" value="{{ request('search') }}" placeholder="Nama, NIK, kode" class="{{ $inputClass }}">
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Status</label>
            <select name="status" class="{{ $inputClass }}">
                <option value="semua">Semua status</option>
                @foreach($statusLabels as $value => $meta)
                    <option value="{{ $value }}" @selected(request('status') === $value)>{{ $meta[0] }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Cabang</label>
            <select name="cabang_asrama_id" class="{{ $inputClass }}"><option value="">Semua cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}" @selected(request('cabang_asrama_id') == $cabang->id)>{{ $cabang->nama }}</option>@endforeach</select>
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Dari tanggal</label>
            <input name="tanggal_dari" type="date" value="{{ request('tanggal_dari') }}" class="{{ $inputClass }}" aria-label="Dari tanggal pengajuan">
        </div>

        <div>
            <label class="mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500">Sampai tanggal</label>
            <input name="tanggal_sampai" type="date" value="{{ request('tanggal_sampai') }}" class="{{ $inputClass }}" aria-label="Sampai tanggal pengajuan">
        </div>

        <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
            <a href="{{ route('admin.reservasi.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <!-- Tabel -->
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[900px] divide-y divide-slate-200 text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">Kode / Pemohon</th>
                        <th class="px-4 py-3.5">Identitas</th>
                        <th class="px-4 py-3.5">Alokasi</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($reservasis as $res)
                        @php
                            $label = $statusLabels[$res->status] ?? [ucfirst($res->status), 'bg-slate-100 text-slate-600'];
                        @endphp
                        <tr class="align-middle transition hover:bg-slate-50/60">

                            {{-- Kode / pemohon --}}
                            <td class="px-4 py-4">
                                <span class="block font-mono text-[11px] text-slate-400">{{ $res->kode_reservasi }}</span>
                                <strong class="mt-0.5 block text-slate-900">{{ $res->user->nama ?? '—' }}</strong>
                            </td>

                            {{-- Identitas --}}
                            <td class="px-4 py-4">
                                <span class="block text-slate-700">{{ $res->user->nik ?? 'NIK belum diverifikasi' }}</span>
                                <span class="block text-xs text-slate-500">{{ $res->user->jenis_kelamin ? ucfirst($res->user->jenis_kelamin) : 'Jenis kelamin belum ditetapkan' }}</span>
                                @if($res->foto_ktp_path)
                                    <a target="_blank" rel="noopener" class="mt-1 inline-block text-xs font-semibold text-[#ed1c24] hover:text-red-700 hover:underline" href="{{ route('dokumen.ktp', $res) }}">Lihat KTP</a>
                                @endif
                            </td>

                            {{-- Alokasi --}}
                            <td class="px-4 py-4">
                                @if($res->penempatans->isNotEmpty())
                                    @foreach($res->penempatans as $placement)
                                        <div class="mb-1 flex items-center gap-2 whitespace-nowrap">
                                            <span class="font-semibold text-slate-800">Kamar {{ $placement->kamar->nomor_kamar ?? '—' }}</span>
                                            <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-medium text-slate-600">{{ $placementLabels[$placement->status] ?? $placement->status }}</span>
                                        </div>
                                        <span class="mb-1 block text-[11px] font-medium text-blue-800">Cabang: {{ $placement->cabangAsrama?->nama ?? 'Belum ditetapkan' }}</span>
                                        <span class="mb-2 block whitespace-nowrap text-[11px] text-slate-500">
                                            {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}
                                            · {{ $res->durasi_bulan ?? '—' }} bulan
                                        </span>
                                    @endforeach
                                    @if($res->jumlah_tagihan !== null)
                                        <span class="block text-[11px] font-semibold text-slate-600">Tagihan: Rp {{ number_format($res->jumlah_tagihan, 0, ',', '.') }}</span>
                                    @endif
                                @elseif($res->durasi_bulan)
                                    <span class="block text-xs font-medium text-slate-700">Permintaan {{ $res->durasi_bulan }} bulan</span>
                                    <span class="text-[11px] text-slate-400">Menunggu tanggal dan alokasi kamar</span>
                                @elseif($res->status === 'approved')
                                    <span class="text-xs font-medium text-amber-700">Reservasi lama; placement belum diverifikasi</span>
                                @else
                                    <span class="text-xs text-slate-400">Belum dialokasikan</span>
                                @endif
                            </td>

                            {{-- Tanggal --}}
                            <td class="whitespace-nowrap px-4 py-4 text-slate-700">
                                {{ \Illuminate\Support\Carbon::parse($res->tanggal_pengajuan)->format('d M Y') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $label[1] }}">{{ $label[0] }}</span>
                            </td>

                            {{-- Tindakan --}}
                            <td class="px-4 py-4">
                                @if($res->status === 'pending')
                                    <div class="flex flex-wrap items-center gap-2">

                                        {{-- Pop up: Setujui --}}
                                        <x-admin.modal title="Setujui Reservasi">
                                            <x-slot name="trigger">
                                                <button type="button" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">Setujui</button>
                                            </x-slot>

                                            <form method="POST" action="{{ route('admin.reservasi.approve', $res) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                    <span class="block font-mono text-slate-400">{{ $res->kode_reservasi }}</span>
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $res->user->nama ?? '—' }}</span>
                                                </div>

                                                <div class="grid grid-cols-2 gap-3">
                                                    <div class="col-span-2">
                                                        <label class="{{ $labelClass }}">Cabang penempatan</label>
                                                        <select name="cabang_asrama_id" required class="{{ $inputClass }}"><option value="">Pilih cabang aktif</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>@endforeach</select>
                                                        <span class="mt-1 block text-[10px] text-slate-500">Kamar dipilih otomatis oleh sistem dari cabang ini, sesuai kategori penghuni dan kapasitas periode.</span>
                                                    </div>
                                                    <div>
                                                        <label class="{{ $labelClass }}">Mulai</label>
                                                        <input type="date" name="tanggal_mulai" required class="{{ $inputClass }}">
                                                    </div>
                                                <div class="rounded-lg bg-white px-3 py-2 text-xs text-slate-600">
                                                    <span class="block font-semibold text-slate-700">Durasi</span>
                                                    @if($res->durasi_bulan)
                                                        {{ $res->durasi_bulan }} bulan (sesuai pengajuan)
                                                    @else
                                                        <select name="durasi_bulan" required class="mt-1 {{ $inputClass }}">
                                                            <option value="">Pilih durasi · maks. 12 bulan</option>
                                                            @for($month = 1; $month <= 12; $month++)
                                                                <option value="{{ $month }}">{{ $month }} bulan</option>
                                                            @endfor
                                                        </select>
                                                        <span class="mt-1 block text-[10px]">Reservasi lama ini belum memiliki durasi numerik.</span>
                                                    @endif
                                                </div>
                                                </div>

                                            <p class="text-[11px] leading-relaxed text-slate-500">Tanggal akhir dihitung dari tanggal mulai dan durasi. Sistem memilih kamar secara acak di cabang terpilih setelah validasi kapasitas, lalu menyimpan snapshot tarif dan tagihan.</p>

                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Alokasikan Kamar</button>
                                                </div>
                                            </form>
                                        </x-admin.modal>

                                        {{-- Pop up: Tolak --}}
                                        <x-admin.modal title="Tolak Pengajuan">
                                            <x-slot name="trigger">
                                                <button type="button" class="rounded-lg border border-rose-300 px-3 py-1.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-50">Tolak</button>
                                            </x-slot>

                                            <form method="POST" action="{{ route('admin.reservasi.reject', $res) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                    <span class="block font-mono text-slate-400">{{ $res->kode_reservasi }}</span>
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $res->user->nama ?? '—' }}</span>
                                                </div>

                                                <div>
                                                    <label class="{{ $labelClass }}">Alasan Penolakan</label>
                                                    <textarea name="catatan_admin" required maxlength="2000" rows="3" placeholder="Tuliskan alasan penolakan" class="{{ $inputClass }}"></textarea>
                                                </div>

                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                    <button class="rounded-lg bg-rose-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-800">Tolak Pengajuan</button>
                                                </div>
                                            </form>
                                        </x-admin.modal>
                                    </div>

                                @elseif($res->status === 'legacy_unverified' && $res->penempatans->isEmpty())
                                    {{-- Pop up: Verifikasi reservasi lama --}}
                                    <x-admin.modal title="Verifikasi Reservasi Lama">
                                        <x-slot name="trigger">
                                            <button type="button" class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-700">Verifikasi</button>
                                        </x-slot>

                                        <form method="POST" action="{{ route('admin.reservasi.verify-legacy', $res) }}" class="space-y-4">
                                            @csrf
                                            @method('PATCH')

                                            <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                <span class="block font-mono text-slate-400">{{ $res->kode_reservasi }}</span>
                                                <span class="block text-sm font-semibold text-slate-900">{{ $res->user->nama ?? '—' }}</span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="col-span-2">
                                                    <label class="{{ $labelClass }}">Cabang untuk penempatan terverifikasi</label>
                                                    <select name="cabang_asrama_id" required class="{{ $inputClass }}"><option value="">Pilih cabang aktif</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}">{{ $cabang->nama }}</option>@endforeach</select>
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">Mulai</label>
                                                    <input type="date" name="tanggal_mulai" required class="{{ $inputClass }}">
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">Akhir (eksklusif)</label>
                                                    <input type="date" name="tanggal_akhir" required class="{{ $inputClass }}">
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                <button class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-700">Verifikasi dan Alokasikan</button>
                                            </div>
                                        </form>
                                    </x-admin.modal>

                                @else
                                    <span class="text-xs text-slate-400">Tidak ada tindakan.</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">Tidak ada reservasi yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">{{ $reservasis->links() }}</div>
    </div>
</x-layouts.admin>
