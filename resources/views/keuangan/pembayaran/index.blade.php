<x-layouts.keuangan title="Verifikasi Pembayaran - Admin Keuangan INSTIKI" activeMenu="pembayaran">

    @php
        $paymentStatusLabels = [
            'pending'  => ['Menunggu', 'bg-amber-100 text-amber-800'],
            'paid'     => ['Lunas', 'bg-emerald-100 text-emerald-800'],
            'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        $inputClass = 'w-full rounded-lg border-slate-300 text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24]';
        $labelClass = 'mb-1 block text-xs font-semibold text-slate-700';
        $filterLabel = 'mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500';
    @endphp

    <!-- Heading -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">Verifikasi Pembayaran</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Dokumen hanya dapat diakses melalui endpoint terotorisasi Admin Keuangan dan pemilik pembayaran.</p>
    </header>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @foreach($errors->all() as $error)
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    <!-- Filter -->
    <form method="GET" action="{{ route('keuangan.pembayaran.index') }}" class="grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label class="{{ $filterLabel }}">Pencarian</label>
            <input name="search" value="{{ request('search') }}" placeholder="Nama atau kode pembayaran" class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $filterLabel }}">Status</label>
            <select name="status" class="{{ $inputClass }}">
                <option value="semua">Semua status</option>
                @foreach(['pending' => 'Menunggu', 'paid' => 'Lunas', 'rejected' => 'Ditolak'] as $key => $statusLabel)
                    <option value="{{ $key }}" @selected(request('status') === $key)>{{ $statusLabel }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="{{ $filterLabel }}">Dari tanggal</label>
            <input type="date" name="tanggal_dari" value="{{ request('tanggal_dari') }}" class="{{ $inputClass }}" aria-label="Dari tanggal">
        </div>

        <div>
            <label class="{{ $filterLabel }}">Sampai tanggal</label>
            <input type="date" name="tanggal_sampai" value="{{ request('tanggal_sampai') }}" class="{{ $inputClass }}" aria-label="Sampai tanggal">
        </div>

        <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
            <a href="{{ route('keuangan.pembayaran.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Reset</a>
        </div>
    </form>

    <!-- Tabel -->
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-left text-sm" style="min-width: 900px;">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">Pembayaran</th>
                        <th class="px-4 py-3.5">Pemilik</th>
                        <th class="px-4 py-3.5">Nominal</th>
                        <th class="px-4 py-3.5">Bukti</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Tindakan</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $payment)
                        @php $label = $paymentStatusLabels[$payment->status] ?? [ucfirst($payment->status), 'bg-slate-100 text-slate-600']; @endphp
                        <tr class="align-middle transition hover:bg-slate-50/60">

                            {{-- Pembayaran --}}
                            <td class="px-4 py-4">
                                <strong class="block font-mono text-slate-900">{{ $payment->kode_pembayaran }}</strong>
                                <span class="block text-[11px] text-slate-400">{{ $payment->created_at->format('d M Y') }}</span>
                                @if($payment->perpanjangan)
                                    <span class="mt-1 block text-[11px] font-semibold text-blue-800">Perpanjangan {{ $payment->perpanjangan->durasi_bulan }} bulan · Kamar {{ $payment->perpanjangan->penempatan?->kamar?->nomor_kamar ?? '—' }}</span>
                                    <span class="block text-[11px] text-blue-700">Cabang {{ $payment->perpanjangan->penempatan?->cabangAsrama?->nama ?? 'Belum tercatat pada placement ini' }}</span>
                                    <span class="block text-[11px] text-slate-600">{{ $payment->perpanjangan->tanggal_akhir_sebelum->format('d M Y') }} – {{ $payment->perpanjangan->tanggal_akhir_diajukan->format('d M Y') }}</span>
                                    <span class="block text-[10px] text-slate-500">Tarif snapshot Rp {{ number_format($payment->perpanjangan->tarif_bulanan, 0, ',', '.') }}/bulan</span>
                                @elseif($payment->reservasi?->durasi_bulan && $payment->reservasi?->tarif_bulanan_disetujui !== null && $payment->reservasi?->jumlah_tagihan !== null)
                                    <span class="mt-1 block text-[11px] text-slate-600">
                                        Tagihan {{ $payment->reservasi->durasi_bulan }} bulan
                                        · {{ $payment->reservasi->tanggal_mulai_disetujui?->format('d M Y') ?? 'tanggal belum tercatat' }} – {{ $payment->reservasi->tanggal_akhir_disetujui?->format('d M Y') ?? 'tanggal belum tercatat' }}
                                    </span>
                                    <span class="block text-[10px] text-slate-500">
                                        Tarif snapshot Rp {{ number_format($payment->reservasi->tarif_bulanan_disetujui, 0, ',', '.') }}/bulan
                                        · Tagihan Rp {{ number_format($payment->reservasi->jumlah_tagihan, 0, ',', '.') }}
                                    </span>
                                @endif
                                @if(!$payment->perpanjangan && $payment->reservasi?->cabang_asrama_id)
                                    <span class="mt-1 block text-[11px] font-medium text-blue-700">Cabang {{ $payment->reservasi->cabangAsrama?->nama ?? 'Belum tercatat pada reservasi ini' }}</span>
                                @endif
                            </td>

                            {{-- Pemilik --}}
                            <td class="px-4 py-4">
                                <span class="block font-semibold text-slate-800">{{ $payment->user->nama ?? '—' }}</span>
                            </td>

                            {{-- Nominal --}}
                            <td class="whitespace-nowrap px-4 py-4 font-bold text-slate-900">Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</td>

                            {{-- Bukti --}}
                            <td class="px-4 py-4">
                                @if($payment->bukti_pembayaran)
                                    <a target="_blank" rel="noopener" href="{{ route('dokumen.pembayaran', $payment) }}" class="inline-block rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Buka bukti</a>
                                @else
                                    <span class="text-xs text-slate-400">Tidak ada file</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $label[1] }}">{{ $label[0] }}</span>
                            </td>

                            {{-- Tindakan --}}
                            <td class="px-4 py-4">
                                @if($payment->status === 'pending')
                                    <div class="flex flex-wrap items-center gap-2">

                                        {{-- Pop up: Verifikasi --}}
                                        <x-admin.modal title="Verifikasi Pembayaran">
                                            <x-slot name="trigger">
                                                <button type="button" class="rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">Verifikasi</button>
                                            </x-slot>

                                            <form method="POST" action="{{ route('keuangan.pembayaran.verify', $payment) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $payment->user->nama ?? '—' }}</span>
                                                    <span class="block">{{ $payment->kode_pembayaran }} · Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</span>
                                                </div>

                                                <p class="text-sm text-slate-600">Tandai pembayaran ini sebagai lunas? Pastikan bukti pembayaran sudah diperiksa.</p>

                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                    <button class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Verifikasi Lunas</button>
                                                </div>
                                            </form>
                                        </x-admin.modal>

                                        {{-- Pop up: Tolak --}}
                                        <x-admin.modal title="Tolak Pembayaran">
                                            <x-slot name="trigger">
                                                <button type="button" class="rounded-lg bg-rose-700 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-800">Tolak</button>
                                            </x-slot>

                                            <form method="POST" action="{{ route('keuangan.pembayaran.reject', $payment) }}" class="space-y-4">
                                                @csrf
                                                @method('PATCH')

                                                <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                    <span class="block text-sm font-semibold text-slate-900">{{ $payment->user->nama ?? '—' }}</span>
                                                    <span class="block">{{ $payment->kode_pembayaran }} · Rp {{ number_format($payment->jumlah_bayar, 0, ',', '.') }}</span>
                                                </div>

                                                <div>
                                                    <label class="{{ $labelClass }}">Alasan Penolakan</label>
                                                    <textarea name="catatan_keuangan" required maxlength="2000" rows="3" placeholder="Tuliskan alasan penolakan" class="{{ $inputClass }}"></textarea>
                                                </div>

                                                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                    <button class="rounded-lg bg-rose-700 px-4 py-2 text-xs font-semibold text-white transition hover:bg-rose-800">Tolak Pembayaran</button>
                                                </div>
                                            </form>
                                        </x-admin.modal>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500">Diputuskan {{ $payment->diputuskan_at?->format('d M Y H:i') ?? 'sebelum metadata tersedia' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-500">Tidak ada pembayaran yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">{{ $pembayarans->links() }}</div>
    </div>
</x-layouts.keuangan>
