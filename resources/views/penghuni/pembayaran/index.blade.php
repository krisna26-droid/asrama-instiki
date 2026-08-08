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
                if (file.type.includes('image')) {
                    this.filePreview = URL.createObjectURL(file);
                } else {
                    this.filePreview = null;
                }
            }
        }
    }">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Pembayaran Saya</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Unggah bukti pembayaran dan pantau status verifikasi sewa Anda</p>
    </div>

    <!-- Alert Notifikasi Flash -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-xs sm:text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-xl text-xs sm:text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <!-- Grid Konten Utama: Unggah Bukti & Lini Masa Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Kondisi Form / Status Informasi -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6">

                @if(!$reservasi)
                    <!-- KONDISI 1: Belum Memiliki Kamar yang Disetujui -->
                    <div class="py-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <h3 class="text-sm font-bold text-slate-800">Belum Ada Tagihan Aktif</h3>
                        <p class="text-xs text-slate-500 max-w-sm mx-auto">
                            Anda belum memiliki reservasi kamar yang disetujui. Silakan lakukan pendaftaran kamar terlebih dahulu pada menu Reservasi Saya.
                        </p>
                    </div>

                @elseif($pembayaranAktif && $pembayaranAktif->status === 'pending')
                    <!-- KONDISI 2: Pembayaran Sedang Diverifikasi (Form Dikunci) -->
                    <div class="p-6 bg-amber-50 border border-amber-200 rounded-xl space-y-3">
                        <div class="flex items-center gap-2 text-amber-900 font-bold text-sm">
                            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Pembayaran Sedang Menunggu Verifikasi</span>
                        </div>
                        <p class="text-xs text-amber-800 leading-relaxed">
                            Bukti pembayaran Anda sebesar <strong class="font-bold">Rp {{ number_format($pembayaranAktif->jumlah_bayar, 0, ',', '.') }}</strong> telah diterima dan saat ini sedang diperiksa oleh Admin Keuangan.
                        </p>
                        <p class="text-[11px] text-amber-700 pt-1">
                            Anda tidak perlu mengunggah ulang bukti transfer. Silakan pantau perkembangan status pada Lini Masa di sebelah kanan.
                        </p>
                    </div>

                @elseif($pembayaranAktif && $pembayaranAktif->status === 'paid')
                    <!-- KONDISI 3: Pembayaran Lunas (Form Dikunci) -->
                    <div class="p-6 bg-emerald-50 border border-emerald-200 rounded-xl space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 text-emerald-900 font-bold text-sm">
                                <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>Pembayaran Sewa Lunas</span>
                            </div>
                            <a href="{{ route('penghuni.pembayaran.kuitansi', $pembayaranAktif->id) }}" target="_blank" 
                               class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition inline-flex items-center gap-1.5 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                Cetak Kuitansi
                            </a>
                        </div>
                        <p class="text-xs text-emerald-800 leading-relaxed">
                            Tagihan sewa kamar Anda untuk periode ini telah dikonfirmasi <strong class="font-bold">Lunas</strong> oleh Admin Keuangan.
                        </p>
                    </div>

                @else
                    <!-- KONDISI 4: Belum Bayar atau Pembayaran Ditolak (Form Terbuka) -->
                    @if($pembayaranAktif && $pembayaranAktif->status === 'rejected')
                        <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-800 space-y-1">
                            <span class="font-bold block">Bukti Pembayaran Sebelumnya Ditolak</span>
                            <p>Catatan Admin: <em>"{{ $pembayaranAktif->catatan_keuangan ?? 'Bukti tidak jelas atau nominal tidak sesuai.' }}"</em></p>
                            <p class="text-[11px] text-rose-600 pt-1">Silakan unggah kembali bukti pembayaran yang benar.</p>
                        </div>
                    @endif

                    <form action="{{ route('penghuni.pembayaran.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                        @csrf
                        
                        <!-- Area Dropzone Unggah File -->
                        <div>
                            <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider mb-3">Unggah Bukti Pembayaran</h3>
                            <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 bg-slate-50 hover:bg-slate-100/50 transition relative text-center flex flex-col items-center justify-center">
                                <input type="file" name="bukti_pembayaran" @change="handleFileChange" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                                
                                <template x-if="!filePreview && !fileName">
                                    <div class="space-y-2">
                                        <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mx-auto">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-700">Tarik atau seret kuitansi pembayaran ke sini</p>
                                        <p class="text-[10px] text-slate-400 font-mono">Format JPG, PNG, atau PDF • Maksimal 5MB</p>
                                    </div>
                                </template>

                                <template x-if="filePreview">
                                    <div class="space-y-2">
                                        <img :src="filePreview" class="max-h-40 rounded-lg shadow-sm mx-auto">
                                        <p class="text-xs font-semibold text-slate-700 font-mono" x-text="fileName"></p>
                                    </div>
                                </template>

                                <template x-if="!filePreview && fileName">
                                    <div class="space-y-2">
                                        <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </div>
                                        <p class="text-xs font-bold text-slate-800 font-mono" x-text="fileName"></p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Ringkasan Pembayaran -->
                        <div class="bg-slate-50 p-5 rounded-xl border border-slate-200/80 space-y-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block border-b border-slate-200/60 pb-2">Ringkasan Tagihan</span>
                            
                            <div class="flex justify-between items-center text-xs">
                                <span class="text-slate-500">Biaya Sewa Kamar ({{ $reservasi->kamar->nomor_kamar ?? '-' }})</span>
                                <span class="font-bold text-slate-800">
                                    Rp {{ number_format($reservasi->kamar->harga_bulanan ?? 850000, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center text-xs border-b border-slate-200/60 pb-3">
                                <span class="text-slate-500">Periode</span>
                                <span class="font-bold text-slate-800">{{ date('F Y') }}</span>
                            </div>

                            <div class="flex justify-between items-center pt-1">
                                <span class="text-xs font-bold text-slate-900">Total Pembayaran</span>
                                <span class="text-sm font-bold text-blue-600">
                                    Rp {{ number_format($reservasi->kamar->harga_bulanan ?? 850000, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>

                        <!-- Tombol Submit -->
                        <button type="submit" class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl shadow transition">
                            Kirim Pembayaran
                        </button>
                    </form>
                @endif

            </div>
        </div>

        <!-- Kolom Kanan: Status Timeline Verifikasi -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-6 h-fit">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Lini Masa Status</h3>

            <div class="space-y-6 relative before:absolute before:inset-0 before:left-3.5 before:w-0.5 before:bg-slate-100 before:z-0">
                
                <!-- Tahap 1: Bukti Dikirim -->
                <div class="flex items-start space-x-3 relative z-10">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 {{ $pembayaranAktif ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Pembayaran Dikirim</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $pembayaranAktif ? \Carbon\Carbon::parse($pembayaranAktif->created_at)->format('d M Y, H:i') : 'Belum diunggah' }}
                        </p>
                    </div>
                </div>

                <!-- Tahap 2: Verifikasi Admin -->
                <div class="flex items-start space-x-3 relative z-10">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 {{ $pembayaranAktif && $pembayaranAktif->status === 'pending' ? 'bg-blue-600 text-white shadow-md ring-4 ring-blue-50' : ($pembayaranAktif && $pembayaranAktif->status === 'paid' ? 'bg-emerald-500 text-white' : ($pembayaranAktif && $pembayaranAktif->status === 'rejected' ? 'bg-rose-500 text-white' : 'bg-slate-100 text-slate-400')) }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Verifikasi Admin</h4>
                        <p class="text-[10px] font-semibold mt-0.5 {{ $pembayaranAktif && $pembayaranAktif->status === 'pending' ? 'text-blue-600' : ($pembayaranAktif && $pembayaranAktif->status === 'rejected' ? 'text-rose-600' : 'text-slate-400') }}">
                            @if(!$pembayaranAktif)
                                Menunggu
                            @elseif($pembayaranAktif->status === 'pending')
                                Sedang Diproses
                            @elseif($pembayaranAktif->status === 'paid')
                                Selesai
                            @else
                                Ditolak
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Tahap 3: Konfirmasi Pembayaran -->
                <div class="flex items-start space-x-3 relative z-10">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 {{ $pembayaranAktif && $pembayaranAktif->status === 'paid' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Pembayaran Dikonfirmasi</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $pembayaranAktif && $pembayaranAktif->status === 'paid' ? 'Lunas Terverifikasi' : 'Menunggu' }}
                        </p>
                    </div>
                </div>

                <!-- Tahap 4: Kuitansi Tersedia -->
                <div class="flex items-start space-x-3 relative z-10">
                    <div class="w-7 h-7 rounded-full flex items-center justify-center shrink-0 {{ $pembayaranAktif && $pembayaranAktif->status === 'paid' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Kuitansi Tersedia</h4>
                        <p class="text-[10px] text-slate-400 mt-0.5">
                            {{ $pembayaranAktif && $pembayaranAktif->status === 'paid' ? 'Dapat Diunduh' : 'Menunggu' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Tabel Riwayat Pembayaran Saya -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-slate-900">Riwayat Pembayaran Saya</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-slate-400 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Periode</th>
                        <th class="py-3 px-4">Jumlah</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Status & Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayatPembayaran as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4 font-mono text-slate-500 font-medium">{{ $item->kode_pembayaran }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-800">{{ \Carbon\Carbon::parse($item->created_at)->format('F Y') }}</td>
                            <td class="py-3.5 px-4 font-bold text-slate-900">Rp {{ number_format($item->jumlah_bayar, 0, ',', '.') }}</td>
                            <td class="py-3.5 px-4 font-mono text-xs text-slate-500">{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d') }}</td>
                            <td class="py-3.5 px-4 flex items-center gap-2">
                                @if($item->status === 'paid')
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Lunas
                                    </span>
                                    <a href="{{ route('penghuni.pembayaran.kuitansi', $item->id) }}" target="_blank" 
                                       class="px-2 py-1 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 rounded-lg text-[10px] font-bold transition inline-flex items-center gap-1">
                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        Cetak PDF
                                    </a>
                                @elseif($item->status === 'pending')
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-100 text-amber-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[10px] font-bold bg-rose-100 text-rose-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400 text-xs">Belum ada riwayat pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</x-layouts.penghuni>