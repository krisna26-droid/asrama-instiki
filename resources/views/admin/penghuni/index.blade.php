<x-layouts.admin title="Penempatan Penghuni - Admin Asrama" activeMenu="penghuni">

    @php
        $placementStatusLabels = [
            'active'    => ['Aktif', 'bg-emerald-100 text-emerald-800'],
            'scheduled' => ['Menunggu check-in', 'bg-blue-100 text-blue-800'],
            'completed' => ['Selesai', 'bg-slate-100 text-slate-600'],
            'cancelled' => ['Dibatalkan', 'bg-slate-100 text-slate-600'],
            'no_show'   => ['Tidak hadir · perlu tindak lanjut', 'bg-red-100 text-red-800'],
        ];
        $inputClass = 'w-full rounded-lg border-slate-300 text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24]';
        $labelClass = 'mb-1 block text-xs font-semibold text-slate-700';
        $filterLabel = 'mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500';
    @endphp

    <!-- Heading -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">Penghuni &amp; Penempatan</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Status aktif hanya menampilkan penghuni yang sudah check-in. Reservasi approved lama tanpa placement tetap perlu verifikasi.</p>
    </header>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @foreach($errors->all() as $error)
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.penghuni.index') }}" class="grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-7">
        <div>
            <label class="{{ $filterLabel }}">Pencarian</label>
            <input name="search" value="{{ request('search') }}" placeholder="Nama, NIK, nomor kamar" class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $filterLabel }}">Cabang</label>
            <select name="cabang_asrama_id" class="{{ $inputClass }}"><option value="">Semua cabang</option>@foreach($cabangs as $cabang)<option value="{{ $cabang->id }}" @selected(request('cabang_asrama_id') == $cabang->id)>{{ $cabang->nama }}</option>@endforeach</select>
        </div>

        <div>
            <label class="{{ $filterLabel }}">Kategori</label>
            <select name="jenis_kelamin" class="{{ $inputClass }}">
                <option value="">Semua kategori</option>
                <option value="putra" @selected(request('jenis_kelamin') === 'putra')>Putra</option>
                <option value="putri" @selected(request('jenis_kelamin') === 'putri')>Putri</option>
            </select>
        </div>

        <div>
            <label class="{{ $filterLabel }}">Status</label>
            <select name="status" class="{{ $inputClass }}">
                <option value="active" @selected($status === 'active')>Penghuni aktif</option>
                <option value="scheduled" @selected($status === 'scheduled')>Penempatan mendatang</option>
                <option value="history" @selected($status === 'history')>Histori</option>
            </select>
        </div>

        <div>
            <label class="{{ $filterLabel }}">Mulai dari</label>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="{{ $inputClass }}" aria-label="Tanggal mulai dari">
        </div>

        <div>
            <label class="{{ $filterLabel }}">Mulai sampai</label>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="{{ $inputClass }}" aria-label="Tanggal mulai sampai">
        </div>

        <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
            <a href="{{ route('admin.penghuni.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <!-- Tabel -->
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-left text-sm" style="min-width: 900px;">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">Penghuni</th>
                        <th class="px-4 py-3.5">NIK / Kategori</th>
                        <th class="px-4 py-3.5">Kamar</th>
                        <th class="px-4 py-3.5">Periode</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($penempatans as $placement)
                        @php
                            $label = $placementStatusLabels[$placement->status] ?? [ucfirst(str_replace('_', ' ', $placement->status)), 'bg-slate-100 text-slate-600'];
                        @endphp
                        <tr class="align-middle transition hover:bg-slate-50/60">

                            {{-- Penghuni --}}
                            <td class="px-4 py-4">
                                <strong class="block text-slate-900">{{ $placement->user->nama ?? '—' }}</strong>
                                <span class="block font-mono text-[11px] text-slate-400">{{ $placement->reservasi->kode_reservasi ?? '' }}</span>
                            </td>

                            {{-- NIK / kategori --}}
                            <td class="px-4 py-4">
                                <span class="block text-slate-700">{{ $placement->user->nik ?? 'Belum diisi' }}</span>
                                <span class="block text-xs text-slate-500">{{ ucfirst($placement->user->jenis_kelamin ?? '—') }}</span>
                            </td>

                            {{-- Kamar --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                @if($placement->kamar)
                                    <span class="block font-semibold text-slate-800">Kamar {{ $placement->kamar->nomor_kamar }}</span>
                                    <span class="block text-xs text-slate-500">Blok {{ $placement->kamar->blok }}</span>
                                    <span class="block text-xs font-medium text-blue-800">{{ $placement->cabangAsrama?->nama ?? 'Cabang belum ditetapkan' }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>

                            {{-- Periode --}}
                            <td class="whitespace-nowrap px-4 py-4 text-slate-700">
                                {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}
                                <span class="block text-xs text-slate-500">
                                    {{ $placement->check_in_at ? 'Check-in '.$placement->check_in_at->format('d M Y H:i') : 'Belum check-in' }}
                                    @if($placement->check_out_at)
                                        <br>Check-out {{ $placement->check_out_at->format('d M Y H:i') }}
                                    @endif
                                </span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $label[1] }}">{{ $label[0] }}</span>
                            </td>

                            {{-- Tindakan --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('admin.penghuni.show', $placement) }}" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Detail</a>

                                    {{-- Pop up: Check-in --}}
                                    @if($placement->status === 'scheduled' && $placement->reservasi?->pembayarans?->contains('status', 'paid'))
                                        <x-admin.modal title="Catat Check-in">
                                            <x-slot name="trigger">
                                                <button type="button" class="rounded-lg bg-emerald-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-800">Check-in</button>
                                            </x-slot>

                                            <form method="POST" action="{{ route('admin.penempatan.check-in', $placement) }}" class="space-y-4">
                                                @csrf

                                                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $placement->user->nama ?? '—' }}</span>
                                                    <span class="block">Kamar {{ $placement->kamar->nomor_kamar ?? '—' }} · {{ $placement->cabangAsrama?->nama ?? 'Cabang belum ditetapkan' }} · {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}</span>
                                                </div>

                                                @if($placement->tanggal_mulai->toDateString() > now()->toDateString())
                                                    <p class="rounded-lg bg-blue-50 px-3 py-2 text-sm text-blue-900">Tanggal ini lebih awal dari jadwal. Setelah kapasitas diperiksa, sistem memulai periode pada hari ini dan menggeser tanggal akhir agar durasi serta nominal tagihan tetap.</p>
                                                @else
                                                    <p class="text-sm text-slate-600">Catat check-in penghuni ini? Waktu check-in aktual akan tersimpan saat ini.</p>
                                                @endif

                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                    <button class="rounded-lg bg-emerald-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-800">Catat Check-in</button>
                                                </div>
                                            </form>
                                        </x-admin.modal>
                                    @elseif($placement->status === 'scheduled' && $placement->tanggal_mulai->toDateString() > now()->toDateString())
                                        <span class="rounded-lg bg-blue-50 px-3 py-1.5 text-xs font-medium text-blue-800">Jadwal check-in {{ $placement->tanggal_mulai->format('d M Y') }}</span>
                                    @elseif($placement->status === 'scheduled' && $placement->tanggal_mulai->toDateString() < now()->toDateString())
                                        <span class="rounded-lg bg-rose-50 px-3 py-1.5 text-xs font-medium text-rose-800">Jadwal terlewat · perlu tindak lanjut</span>
                                    @elseif($placement->status === 'scheduled')
                                        <span class="rounded-lg bg-amber-50 px-3 py-1.5 text-xs font-medium text-amber-800">Menunggu pembayaran lunas sebelum check-in</span>
                                    @endif

                                    {{-- Pop up: Check-out --}}
                                    @if($placement->status === 'active')
                                        <x-admin.modal title="Catat Check-out">
                                            <x-slot name="trigger">
                                                <button type="button" class="rounded-lg bg-amber-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-amber-700">Check-out</button>
                                            </x-slot>

                                            <form method="POST" action="{{ route('admin.penempatan.check-out', $placement) }}" class="space-y-4">
                                                @csrf

                                                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $placement->user->nama ?? '—' }}</span>
                                                    <span class="block">Kamar {{ $placement->kamar->nomor_kamar ?? '—' }} · {{ $placement->cabangAsrama?->nama ?? 'Cabang belum ditetapkan' }} · {{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}</span>
                                                </div>

                                                <div>
                                                    <label class="{{ $labelClass }}">Alasan Check-out</label>
                                                    <textarea name="alasan" required maxlength="2000" rows="3" placeholder="Tuliskan alasan check-out" class="{{ $inputClass }}"></textarea>
                                                </div>

                                                <p class="rounded-lg bg-amber-50 px-3 py-2 text-[11px] leading-relaxed text-amber-800">Check-out dini akan membatalkan penempatan mendatang milik penghuni ini.</p>

                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                    <button class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-700">Catat Check-out</button>
                                                </div>
                                            </form>
                                        </x-admin.modal>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">Tidak ada penempatan pada kategori ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">{{ $penempatans->links() }}</div>
    </div>
</x-layouts.admin>
