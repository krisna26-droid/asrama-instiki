<x-layouts.penghuni
    title="Pembayaran Saya - Asrama INSTIKI"
    activeMenu="pembayaran"
    x-data="{
        filePreview: null,
        fileName: '',
        handleFileChange(e) {
            const file = e.target.files[0];
            if (file) {
                this.fileName = file.name;
                this.filePreview = file.type.includes('image') ? URL.createObjectURL(file) : null;
            }
        }
    }">

    @php
        $status = $pembayaranAktif?->status;
        $pembayaranLabels = [
            'pending'  => ['Menunggu', 'bg-amber-100 text-amber-800'],
            'paid'     => ['Lunas', 'bg-emerald-100 text-emerald-800'],
            'rejected' => ['Ditolak', 'bg-red-100 text-red-800'],
        ];
        $printIcon = 'M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z';
    @endphp

    <!-- Heading -->
    <header>
        <h1 class="text-2xl font-bold text-slate-900">Pembayaran Saya</h1>
        <p class="mt-1 text-xs text-slate-500 sm:text-sm">Unggah bukti pembayaran dan pantau status verifikasi sewa Anda.</p>
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

    <div class="grid grid-cols-1 items-start gap-6 xl:grid-cols-3">

        <!-- Kolom Kiri: Form / Status -->
        <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6 xl:col-span-2">

            @if($reservasi)
                <section class="rounded-lg border border-blue-100 bg-blue-50/60 p-4">
                    <h2 class="text-xs font-bold uppercase tracking-wide text-blue-900">Kamar dan periode reservasi</h2>
                    @if($reservasi->tanggal_mulai_disetujui && $reservasi->tanggal_akhir_disetujui)
                        <p class="mt-2 text-sm font-semibold text-slate-900">Tagihan awal: {{ $reservasi->durasi_bulan }} bulan · {{ $reservasi->tanggal_mulai_disetujui->format('d M Y') }} – {{ $reservasi->tanggal_akhir_disetujui->format('d M Y') }}</p>
                        <p class="mt-1 text-[11px] text-slate-500">Tanggal akhir adalah batas checkout dan tidak termasuk dalam interval hunian. Perpanjangan, bila disetujui, ditampilkan pada placement.</p>
                    @else
                        <p class="mt-2 text-sm font-semibold text-slate-900">Durasi dan periode tagihan belum terverifikasi untuk reservasi lama.</p>
                    @endif
                    @foreach($reservasi->penempatans->whereIn('status', ['scheduled', 'active'])->sortBy('tanggal_mulai') as $placement)
                        <p class="mt-2 text-xs text-slate-700">
                            Kamar {{ $placement->kamar?->nomor_kamar ?? 'menunggu alokasi' }}
                            @if($placement->kamar) · Blok {{ $placement->kamar->blok }}@endif
                            · Cabang {{ $placement->cabangAsrama?->nama ?? 'belum tercatat' }}
                            — {{ $placement->status === 'active' ? 'aktif' : 'menunggu check-in' }}
                        </p>
                    @endforeach
                    <p class="mt-2 text-[11px] font-medium text-blue-900">Admin Asrama mencatat check-in setelah pembayaran lunas diverifikasi Admin Keuangan.</p>
                </section>
            @endif

            @if(!$reservasi)
                <!-- Belum punya reservasi disetujui -->
                <div class="space-y-3 py-10 text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Belum Ada Tagihan Aktif</h2>
                    <p class="mx-auto max-w-sm text-xs text-slate-500">Anda belum memiliki reservasi kamar yang disetujui. Silakan lakukan pendaftaran kamar terlebih dahulu pada menu Reservasi Saya.</p>
                </div>

            @elseif($status === 'pending')
                <!-- Menunggu verifikasi -->
                <div class="space-y-2 rounded-lg border border-amber-200 bg-amber-50 p-5">
                    <div class="flex items-center gap-2 text-sm font-bold text-amber-900">
                        <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Pembayaran Sedang Menunggu Verifikasi
                    </div>
                    <p class="text-xs leading-relaxed text-amber-800">Bukti pembayaran Anda sebesar <strong>Rp {{ number_format($pembayaranAktif->jumlah_bayar, 0, ',', '.') }}</strong> telah diterima dan sedang diperiksa oleh Admin Keuangan.</p>
                    <p class="text-[11px] text-amber-700">Anda tidak perlu mengunggah ulang bukti transfer. Pantau perkembangan status pada Lini Masa.</p>
                    @if($pembayaranAktif->bukti_pembayaran)<a target="_blank" rel="noopener" href="{{ route('dokumen.pembayaran', $pembayaranAktif) }}" class="inline-flex text-xs font-semibold text-amber-900 underline">Lihat bukti yang dikirim</a>@endif
                </div>

            @elseif($status === 'paid')
                <!-- Lunas -->
                <div class="space-y-2 rounded-lg border border-emerald-200 bg-emerald-50 p-5">
                    <div class="flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-sm font-bold text-emerald-900">
                            <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Pembayaran Sewa Lunas
                        </div>
                        <a href="{{ route('penghuni.pembayaran.kuitansi', $pembayaranAktif->id) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 rounded-lg bg-emerald-600 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $printIcon }}"/></svg>
                            Cetak Kuitansi
                        </a>
                    </div>
                    <p class="text-xs leading-relaxed text-emerald-800">Tagihan sewa kamar Anda untuk periode ini telah dikonfirmasi <strong>Lunas</strong> oleh Admin Keuangan.</p>
                    @if($pembayaranAktif->bukti_pembayaran)<a target="_blank" rel="noopener" href="{{ route('dokumen.pembayaran', $pembayaranAktif) }}" class="inline-flex text-xs font-semibold text-emerald-900 underline">Lihat bukti yang dikirim</a>@endif
                </div>

            @else
                <!-- Belum bayar / ditolak: form terbuka -->
                @if($status === 'rejected')
                    <div class="space-y-1 rounded-lg border border-rose-200 bg-rose-50 p-4 text-xs text-rose-800">
                        <span class="block font-bold">Bukti Pembayaran Sebelumnya Ditolak</span>
                        <p>Catatan Admin: <em>"{{ $pembayaranAktif->catatan_keuangan ?? 'Bukti tidak jelas atau nominal tidak sesuai.' }}"</em></p>
                        <p class="text-[11px] text-rose-600">Silakan unggah kembali bukti pembayaran yang benar.</p>
                        @if($pembayaranAktif->bukti_pembayaran)<a target="_blank" rel="noopener" href="{{ route('dokumen.pembayaran', $pembayaranAktif) }}" class="inline-flex text-xs font-semibold text-rose-800 underline">Lihat bukti sebelumnya</a>@endif
                    </div>
                @endif

                @if($reservasi->jumlah_tagihan === null || $reservasi->durasi_bulan === null || $reservasi->tarif_bulanan_disetujui === null)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-xs leading-relaxed text-amber-900">
                        Rincian tagihan reservasi lama belum dapat dihitung dengan aman. Hubungi Admin Asrama untuk memverifikasi periode dan tarif sebelum mengirim pembayaran.
                    </div>
                @else
                <form action="{{ route('penghuni.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Dropzone -->
                    <div>
                        <h2 class="mb-3 text-base font-bold text-slate-900">Unggah Bukti Pembayaran</h2>
                        <div class="relative flex flex-col items-center justify-center rounded-lg border-2 border-dashed border-slate-300 bg-slate-50 p-8 text-center transition hover:border-[#ed1c24] hover:bg-red-50/30">
                            <input type="file" name="bukti_pembayaran" accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" @change="handleFileChange" required class="absolute inset-0 z-10 h-full w-full cursor-pointer opacity-0">

                            <template x-if="!filePreview && !fileName">
                                <div class="space-y-2">
                                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-lg bg-red-50 text-[#ed1c24]">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
                                    </div>
                                    <p class="text-xs font-bold text-slate-700">Tarik atau seret kuitansi pembayaran ke sini</p>
                                    <p class="font-mono text-[11px] text-slate-400">Format JPG, PNG, atau PDF • Maksimal 5MB</p>
                                </div>
                            </template>

                            <template x-if="filePreview">
                                <div class="space-y-2">
                                    <img :src="filePreview" class="mx-auto max-h-40 rounded-lg shadow-sm" alt="Pratinjau bukti">
                                    <p class="font-mono text-xs font-semibold text-slate-700" x-text="fileName"></p>
                                </div>
                            </template>

                            <template x-if="!filePreview && fileName">
                                <div class="space-y-2">
                                    <div class="mx-auto flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <p class="font-mono text-xs font-bold text-slate-800" x-text="fileName"></p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Ringkasan -->
                    <div class="space-y-3 rounded-lg border border-slate-200 bg-slate-50 p-5">
                        <span class="block border-b border-slate-200 pb-2 text-[11px] font-bold uppercase tracking-wider text-slate-400">Ringkasan Tagihan</span>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Tarif per bulan</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($reservasi->tarif_bulanan_disetujui, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">Durasi reservasi</span>
                            <span class="font-bold text-slate-800">{{ $reservasi->durasi_bulan }} bulan</span>
                        </div>

                        <div class="flex items-center justify-between border-b border-slate-200 pb-3 text-xs">
                            <span class="text-slate-500">Periode</span>
                            <span class="font-bold text-slate-800">{{ $reservasi->tanggal_mulai_disetujui?->format('d M Y') }} – {{ $reservasi->tanggal_akhir_disetujui?->format('d M Y') }}</span>
                        </div>

                        <div class="flex items-center justify-between pt-1">
                            <span class="text-xs font-bold text-slate-900">Total Pembayaran</span>
                            <span class="text-sm font-bold text-[#ed1c24]">Rp {{ number_format($reservasi->jumlah_tagihan, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">Kirim Pembayaran</button>
                </form>
                @endif
            @endif
        </div>

        <!-- Kolom Kanan: Lini Masa -->
        <div class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
            <h2 class="mb-5 border-b border-slate-100 pb-4 text-base font-bold text-slate-900">Lini Masa Status</h2>

            @php
                $done = 'bg-emerald-500 text-white';
                $idle = 'bg-slate-100 text-slate-400';
                $verifClass = match($status) {
                    'pending'  => 'bg-amber-500 text-white ring-4 ring-amber-100',
                    'paid'     => $done,
                    'rejected' => 'bg-rose-500 text-white',
                    default    => $idle,
                };
                $verifText = match($status) {
                    null       => 'Menunggu',
                    'pending'  => 'Sedang Diproses',
                    'paid'     => 'Selesai',
                    default    => 'Ditolak',
                };
                $verifTextClass = match($status) {
                    'pending'  => 'text-amber-600',
                    'rejected' => 'text-rose-600',
                    default    => 'text-slate-400',
                };
            @endphp

            <div class="relative space-y-6">
                <div class="absolute bottom-3 left-3.5 top-3 w-0.5 bg-slate-100"></div>

                <!-- 1 -->
                <div class="relative z-10 flex items-start gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $pembayaranAktif ? $done : $idle }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Pembayaran Dikirim</h3>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ $pembayaranAktif ? \Carbon\Carbon::parse($pembayaranAktif->created_at)->format('d M Y, H:i') : 'Belum diunggah' }}</p>
                    </div>
                </div>

                <!-- 2 -->
                <div class="relative z-10 flex items-start gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $verifClass }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Verifikasi Admin</h3>
                        <p class="mt-0.5 text-[11px] font-semibold {{ $verifTextClass }}">{{ $verifText }}</p>
                    </div>
                </div>

                <!-- 3 -->
                <div class="relative z-10 flex items-start gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $status === 'paid' ? $done : $idle }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Pembayaran Dikonfirmasi</h3>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ $status === 'paid' ? 'Lunas Terverifikasi' : 'Menunggu' }}</p>
                    </div>
                </div>

                <!-- 4 -->
                <div class="relative z-10 flex items-start gap-3">
                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full {{ $status === 'paid' ? $done : $idle }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-slate-900">Kuitansi Tersedia</h3>
                        <p class="mt-0.5 text-[11px] text-slate-400">{{ $status === 'paid' ? 'Dapat Diunduh' : 'Menunggu' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Perpanjangan masa tinggal: satu bulan tiap pengajuan, berbasis kapasitas kamar. --}}
    <section class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div>
            <h2 class="text-base font-bold text-slate-900">Perpanjangan masa tinggal</h2>
            <p class="mt-1 max-w-3xl text-sm leading-6 text-slate-600">Ajukan satu bulan tambahan sebelum tanggal akhir. Admin Asrama memeriksa kapasitas kamar; setelah disetujui, kamar ditahan untuk periode tambahan sambil menunggu pembayaran. Perpanjangan berlaku setelah Admin Keuangan memverifikasi pembayaran.</p>
        </div>

        @if($activePlacement)
            @php
                $placementExtensions = $extensions->where('penempatan_id', $activePlacement->id);
                $hasOpenExtension = $placementExtensions->contains(fn ($item) => in_array($item->status, ['pending', 'approved'], true));
            @endphp
            <div class="rounded-lg border border-blue-100 bg-blue-50/60 p-4 text-sm text-blue-950">
                <p class="font-semibold">Kamar {{ $activePlacement->kamar?->nomor_kamar ?? '—' }} · {{ $activePlacement->cabangAsrama?->nama ?? 'Cabang belum tercatat' }} · Periode saat ini berakhir {{ $activePlacement->tanggal_akhir->format('d M Y') }}</p>
                <p class="mt-1 text-xs leading-5 text-blue-800">Tanggal akhir tidak termasuk dalam masa hunian. Unggah bukti sebelum tanggal tersebut. Jika belum ada pembayaran yang diverifikasi, masa tinggal berakhir dan kamar kembali tersedia.</p>
            </div>

            @if(!$hasOpenExtension && now()->toDateString() < $activePlacement->tanggal_akhir->toDateString())
                <form method="POST" action="{{ route('penghuni.perpanjangan.store', $activePlacement) }}" onsubmit="return confirm('Ajukan perpanjangan satu bulan untuk kamar ini?')">
                    @csrf
                    <button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">Ajukan perpanjangan 1 bulan</button>
                </form>
            @elseif(!$hasOpenExtension)
                <p class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900">Masa pengajuan perpanjangan sudah berakhir. Jika ingin tinggal kembali, ajukan reservasi baru.</p>
            @endif
        @else
            <p class="rounded-lg border border-slate-200 bg-slate-50 p-3 text-sm text-slate-600">Pengajuan perpanjangan tersedia setelah Anda check-in dan memiliki penempatan aktif.</p>
        @endif

        @if($extensions->isNotEmpty())
            <div class="space-y-3">
                <h3 class="text-sm font-bold text-slate-800">Riwayat dan status perpanjangan</h3>
                @foreach($extensions as $extension)
                    @php
                        $extensionPayment = $extension->pembayarans->sortByDesc('id')->first();
                        $extensionLabel = match($extension->status) {
                            'pending' => ['Menunggu persetujuan Admin Asrama', 'bg-amber-100 text-amber-800'],
                            'approved' => ['Disetujui · menunggu pembayaran', 'bg-blue-100 text-blue-800'],
                            'completed' => ['Lunas dan diperpanjang', 'bg-emerald-100 text-emerald-800'],
                            'rejected' => ['Ditolak', 'bg-rose-100 text-rose-800'],
                            'expired' => ['Kedaluwarsa · masa tinggal berakhir', 'bg-slate-100 text-slate-700'],
                            'cancelled' => ['Dibatalkan', 'bg-slate-100 text-slate-700'],
                            default => [ucfirst($extension->status), 'bg-slate-100 text-slate-700'],
                        };
                    @endphp
                    <article class="rounded-lg border border-slate-200 p-4">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Kamar {{ $extension->penempatan?->kamar?->nomor_kamar ?? '—' }} · {{ $extension->penempatan?->cabangAsrama?->nama ?? 'Cabang belum tercatat' }} · 1 bulan</p>
                                <p class="mt-1 text-xs text-slate-600">{{ $extension->tanggal_akhir_sebelum->format('d M Y') }} – {{ $extension->tanggal_akhir_diajukan->format('d M Y') }}</p>
                                <p class="mt-1 text-xs font-semibold text-slate-700">Tagihan: Rp {{ number_format($extension->jumlah_tagihan, 0, ',', '.') }}</p>
                            </div>
                            <span class="rounded-full px-2.5 py-1 text-[11px] font-bold {{ $extensionLabel[1] }}">{{ $extensionLabel[0] }}</span>
                        </div>

                        @if($extension->catatan_admin)
                            <p class="mt-2 text-xs text-slate-600">Catatan Admin Asrama: {{ $extension->catatan_admin }}</p>
                        @endif

                        @if($extension->status === 'approved')
                            @if($extensionPayment?->status === 'pending')
                                <p class="mt-3 rounded-lg bg-amber-50 p-3 text-xs text-amber-900">Bukti pembayaran sudah dikirim dan menunggu verifikasi Admin Keuangan.</p>
                            @elseif($extensionPayment?->status === 'paid')
                                <p class="mt-3 rounded-lg bg-emerald-50 p-3 text-xs text-emerald-900">Pembayaran sudah lunas.</p>
                            @else
                                @if($extensionPayment?->status === 'rejected')
                                    <p class="mb-2 rounded-lg bg-rose-50 p-3 text-xs text-rose-800">Pembayaran sebelumnya ditolak: {{ $extensionPayment->catatan_keuangan ?? 'Silakan periksa bukti dan unggah ulang.' }}</p>
                                @endif
                                @if(now()->toDateString() < $extension->tanggal_akhir_sebelum->toDateString())
                                    <form method="POST" action="{{ route('penghuni.perpanjangan.payment.store', $extension) }}" enctype="multipart/form-data" class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-end">
                                        @csrf
                                        <label class="block flex-1 text-xs font-semibold text-slate-700">Bukti pembayaran (JPG, PNG, PDF; maks. 5 MB)
                                            <input type="file" name="bukti_pembayaran" required accept=".jpg,.jpeg,.png,.pdf,image/jpeg,image/png,application/pdf" class="mt-1 block w-full rounded-lg border border-slate-300 p-2 text-xs">
                                        </label>
                                        <button class="rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700">Kirim bukti perpanjangan</button>
                                    </form>
                                @else
                                    <p class="mt-3 text-xs font-medium text-rose-700">Batas pembayaran telah lewat. Perpanjangan tidak berlaku.</p>
                                @endif
                            @endif
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Riwayat Pembayaran -->
    <div class="space-y-4 rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Riwayat Pembayaran Saya</h2>
            <p class="text-xs text-slate-500">Seluruh transaksi pembayaran yang pernah Anda kirim.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[720px] text-left text-xs text-slate-600 sm:text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-700">
                    <tr>
                        <th class="px-4 py-3.5">Kode</th>
                        <th class="px-4 py-3.5">Periode</th>
                        <th class="px-4 py-3.5">Jumlah</th>
                        <th class="px-4 py-3.5">Tanggal</th>
                        <th class="px-4 py-3.5">Status</th>
                        <th class="px-4 py-3.5">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayatPembayaran as $item)
                        @php $label = $pembayaranLabels[$item->status] ?? [ucfirst($item->status), 'bg-slate-100 text-slate-600']; @endphp
                        <tr class="transition hover:bg-slate-50">
                            <td class="whitespace-nowrap px-4 py-3.5 font-mono font-bold text-slate-800">{{ $item->kode_pembayaran }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5 font-semibold text-slate-800">
                                @if($item->perpanjangan)
                                    Perpanjangan · {{ $item->perpanjangan->tanggal_akhir_sebelum->format('d M') }}–{{ $item->perpanjangan->tanggal_akhir_diajukan->format('d M Y') }}
                                @else
                                    {{ \Carbon\Carbon::parse($item->created_at)->format('F Y') }}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-4 py-3.5 font-bold text-slate-900">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="whitespace-nowrap px-4 py-3.5">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                            <td class="px-4 py-3.5">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $label[1] }}">{{ $label[0] }}</span>
                            </td>
                            <td class="px-4 py-3.5">
                                @if($item->bukti_pembayaran)
                                    <a href="{{ route('dokumen.pembayaran', $item) }}" target="_blank" rel="noopener"
                                       class="mb-1 inline-flex rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        Lihat bukti
                                    </a>
                                @endif
                                @if($item->status === 'paid')
                                    <a href="{{ route('penghuni.pembayaran.kuitansi', $item->id) }}" target="_blank"
                                       class="inline-flex items-center gap-1 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                        <svg class="h-3.5 w-3.5 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $printIcon }}"/></svg>
                                        Cetak PDF
                                    </a>
                                @elseif(!$item->bukti_pembayaran)
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-sm text-slate-400">Belum ada riwayat pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.penghuni>
