<x-layouts.admin title="Perpanjangan Sewa - Admin Asrama INSTIKI" activeMenu="perpanjangan">
    <header>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#ed1c24]">Lifecycle penghuni</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Perpanjangan Masa Tinggal</h1>
        <p class="mt-1 text-sm text-slate-500">Setujui hanya jika kamar masih memiliki kapasitas pada seluruh periode tambahan. Persetujuan menahan kapasitas sampai pembayaran diputuskan.</p>
    </header>

    @if(session('success'))
        <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif
    @foreach($errors->all() as $error)
        <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    <form method="GET" action="{{ route('admin.perpanjangan.index') }}" class="grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-3">
        <label class="text-xs font-semibold text-slate-600">Cari penghuni atau kamar
            <input name="search" value="{{ request('search') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm" placeholder="Nama, NIK, atau nomor kamar">
        </label>
        <label class="text-xs font-semibold text-slate-600">Status
            <select name="status" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                @foreach(['pending' => 'Menunggu', 'approved' => 'Disetujui / menunggu pembayaran', 'completed' => 'Lunas', 'rejected' => 'Ditolak', 'expired' => 'Kedaluwarsa', 'semua' => 'Semua status'] as $value => $label)
                    <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </label>
        <div class="flex gap-2">
            <button class="flex-1 rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white">Terapkan</button>
            <a href="{{ route('admin.perpanjangan.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700">Reset</a>
        </div>
    </form>

    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full min-w-[980px] text-left text-sm">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3">Penghuni</th>
                        <th class="px-4 py-3">Kamar dan Periode</th>
                        <th class="px-4 py-3">Tagihan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($extensions as $extension)
                        @php
                            $statusLabel = match($extension->status) {
                                'pending' => ['Menunggu persetujuan', 'bg-amber-100 text-amber-800'],
                                'approved' => ['Menunggu pembayaran', 'bg-blue-100 text-blue-800'],
                                'completed' => ['Lunas dan diperpanjang', 'bg-emerald-100 text-emerald-800'],
                                'rejected' => ['Ditolak', 'bg-rose-100 text-rose-800'],
                                'expired' => ['Kedaluwarsa', 'bg-slate-100 text-slate-700'],
                                'cancelled' => ['Dibatalkan', 'bg-slate-100 text-slate-700'],
                                default => [ucfirst($extension->status), 'bg-slate-100 text-slate-700'],
                            };
                        @endphp
                        <tr class="align-top">
                            <td class="px-4 py-4">
                                <strong class="block text-slate-900">{{ $extension->user->nama ?? '—' }}</strong>
                                <span class="text-xs text-slate-500">NIK {{ $extension->user->nik ?? '—' }}</span>
                            </td>
                            <td class="px-4 py-4">
                                <strong class="block text-slate-900">Kamar {{ $extension->penempatan?->kamar?->nomor_kamar ?? '—' }}</strong>
                                <span class="block text-xs text-slate-600">Sebelum: {{ $extension->tanggal_akhir_sebelum->format('d M Y') }}</span>
                                <span class="block text-xs text-slate-600">Jika disetujui dan lunas: sampai {{ $extension->tanggal_akhir_diajukan->format('d M Y') }}</span>
                                <span class="mt-1 block text-[11px] text-slate-400">Tanggal akhir bersifat eksklusif. Pengajuan {{ $extension->diajukan_at?->format('d M Y H:i') ?? $extension->created_at?->format('d M Y H:i') }}</span>
                            </td>
                            <td class="whitespace-nowrap px-4 py-4">
                                <strong>Rp {{ number_format($extension->jumlah_tagihan, 0, ',', '.') }}</strong>
                                <span class="block text-xs text-slate-500">Tarif tersimpan Rp {{ number_format($extension->tarif_bulanan, 0, ',', '.') }}/bulan</span>
                                @foreach($extension->pembayarans as $payment)
                                    <span class="mt-1 block text-xs text-slate-600">{{ $payment->kode_pembayaran }} · {{ $payment->status }}</span>
                                @endforeach
                            </td>
                            <td class="px-4 py-4">
                                <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-bold {{ $statusLabel[1] }}">{{ $statusLabel[0] }}</span>
                                @if($extension->catatan_admin)
                                    <p class="mt-2 max-w-xs whitespace-pre-line text-xs text-slate-500">{{ $extension->catatan_admin }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @if($extension->status === 'pending')
                                    <div class="space-y-3">
                                        <form method="POST" action="{{ route('admin.perpanjangan.approve', $extension) }}" onsubmit="return confirm('Setujui perpanjangan ini? Kapasitas kamar akan ditahan sambil menunggu pembayaran.')" class="space-y-2">
                                            @csrf @method('PATCH')
                                            <input name="catatan_admin" maxlength="2000" placeholder="Catatan persetujuan (opsional)" class="w-full rounded-lg border-slate-300 text-xs">
                                            <button class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-xs font-semibold text-white hover:bg-emerald-700">Setujui jika kapasitas cukup</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.perpanjangan.reject', $extension) }}" onsubmit="return confirm('Tolak pengajuan perpanjangan ini?')" class="space-y-2">
                                            @csrf @method('PATCH')
                                            <textarea name="catatan_admin" required maxlength="2000" rows="2" placeholder="Alasan penolakan" class="w-full rounded-lg border-slate-300 text-xs"></textarea>
                                            <button class="w-full rounded-lg border border-rose-300 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Tolak</button>
                                        </form>
                                    </div>
                                @elseif($extension->status === 'approved')
                                    @if($extension->pembayarans->contains(fn ($payment) => in_array($payment->status, ['pending', 'paid'], true)))
                                        <p class="max-w-xs text-xs text-amber-800">Pembayaran masih menunggu keputusan. Admin Keuangan perlu menuntaskannya sebelum persetujuan perpanjangan dibatalkan.</p>
                                    @else
                                        <form method="POST" action="{{ route('admin.perpanjangan.cancel', $extension) }}" onsubmit="return confirm('Batalkan persetujuan perpanjangan dan lepaskan komitmen kapasitas?')" class="space-y-2">
                                            @csrf @method('PATCH')
                                            <textarea name="catatan_admin" required maxlength="2000" rows="2" placeholder="Alasan pembatalan" class="w-full rounded-lg border-slate-300 text-xs"></textarea>
                                            <button class="w-full rounded-lg border border-amber-300 px-3 py-2 text-xs font-semibold text-amber-800 hover:bg-amber-50">Batalkan persetujuan</button>
                                        </form>
                                    @endif
                                @else
                                    <span class="text-xs text-slate-500">Tidak ada tindakan</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-4 py-12 text-center text-sm text-slate-500">Belum ada pengajuan perpanjangan pada filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-100 p-4">{{ $extensions->links() }}</div>
    </div>
</x-layouts.admin>
