<x-layouts.penghuni 
    title="Reservasi Saya - Asrama INSTIKI" 
    activeMenu="reservasi"
    x-data="{ 
        step: 1, 
        selectedKamar: null,
        kamarData: {},
        ktpPreview: null,
        ktmPreview: null,
        selectKamar(kamar) {
            this.selectedKamar = kamar.id;
            this.kamarData = kamar;
        },
        handleKtpUpload(e) {
            const file = e.target.files[0];
            if(file) this.ktpPreview = URL.createObjectURL(file);
        },
        handleKtmUpload(e) {
            const file = e.target.files[0];
            if(file) this.ktmPreview = URL.createObjectURL(file);
        }
    }">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Reservasi Saya</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Selesaikan pengajuan reservasi kamar Anda langkah demi langkah</p>
    </div>

    @if($reservasiAktif)
        <!-- Kartu Tampilan Jika Sudah Punya Pengajuan/Kamar -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-xs text-slate-400 block font-mono">Kode Reservasi: {{ $reservasiAktif->kode_reservasi }}</span>
                    <h3 class="text-lg font-bold text-slate-900 mt-0.5">Status Pengajuan Kamar</h3>
                </div>
                <div>
                    @if($reservasiAktif->status === 'approved')
                        <span class="px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-800 rounded-full inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            Disetujui
                        </span>
                    @elseif($reservasiAktif->status === 'pending')
                        <span class="px-3 py-1 text-xs font-bold bg-amber-100 text-amber-800 rounded-full inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="px-3 py-1 text-xs font-bold bg-rose-100 text-rose-800 rounded-full inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                            Ditolak
                        </span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block">Pilihan Kamar</span>
                    <span class="font-bold text-slate-800 text-sm">
                        {{ $reservasiAktif->kamar ? 'Kamar ' . $reservasiAktif->kamar->nomor_kamar : '-' }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block">Tanggal Pengajuan</span>
                    <span class="font-bold text-slate-800 text-sm">
                        {{ \Carbon\Carbon::parse($reservasiAktif->tanggal_pengajuan)->format('d F Y') }}
                    </span>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-slate-400 block">Durasi Sewa</span>
                    <span class="font-bold text-slate-800 text-sm">{{ $reservasiAktif->durasi_sewa }}</span>
                </div>
            </div>
        </div>
    @else
        <!-- Multi-Step Wizard Card -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-8">
            
            <!-- STEPPER INDICATOR (1 s.d 5) -->
            <div class="flex items-center justify-between max-w-3xl mx-auto px-2">
                <!-- Step 1 -->
                <div class="flex flex-col items-center space-y-1.5 z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition"
                         :class="step >= 1 ? 'bg-blue-600 text-white ring-4 ring-blue-50' : 'bg-slate-100 text-slate-400'">1</div>
                    <span class="text-[11px] font-semibold text-slate-600 hidden sm:block">Pilih Kamar</span>
                </div>
                <div class="h-0.5 flex-1 mx-2 transition" :class="step > 1 ? 'bg-blue-600' : 'bg-slate-200'"></div>

                <!-- Step 2 -->
                <div class="flex flex-col items-center space-y-1.5 z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition"
                         :class="step >= 2 ? 'bg-blue-600 text-white ring-4 ring-blue-50' : 'bg-slate-100 text-slate-400'">2</div>
                    <span class="text-[11px] font-semibold text-slate-600 hidden sm:block">Unggah KTP</span>
                </div>
                <div class="h-0.5 flex-1 mx-2 transition" :class="step > 2 ? 'bg-blue-600' : 'bg-slate-200'"></div>

                <!-- Step 3 -->
                <div class="flex flex-col items-center space-y-1.5 z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition"
                         :class="step >= 3 ? 'bg-blue-600 text-white ring-4 ring-blue-50' : 'bg-slate-100 text-slate-400'">3</div>
                    <span class="text-[11px] font-semibold text-slate-600 hidden sm:block">Unggah KTM</span>
                </div>
                <div class="h-0.5 flex-1 mx-2 transition" :class="step > 3 ? 'bg-blue-600' : 'bg-slate-200'"></div>

                <!-- Step 4 -->
                <div class="flex flex-col items-center space-y-1.5 z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition"
                         :class="step >= 4 ? 'bg-blue-600 text-white ring-4 ring-blue-50' : 'bg-slate-100 text-slate-400'">4</div>
                    <span class="text-[11px] font-semibold text-slate-600 hidden sm:block">Tinjau</span>
                </div>
                <div class="h-0.5 flex-1 mx-2 transition" :class="step > 4 ? 'bg-blue-600' : 'bg-slate-200'"></div>

                <!-- Step 5 -->
                <div class="flex flex-col items-center space-y-1.5 z-10">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs transition"
                         :class="step >= 5 ? 'bg-blue-600 text-white ring-4 ring-blue-50' : 'bg-slate-100 text-slate-400'">5</div>
                    <span class="text-[11px] font-semibold text-slate-600 hidden sm:block">Kirim</span>
                </div>
            </div>

            <!-- FORM UTAMA -->
            <form action="{{ route('penghuni.reservasi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="kamar_id" :value="selectedKamar">

                <!-- LANGKAH 1: PILIH KAMAR -->
                <div x-show="step === 1" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Langkah 1: Pilih Kamar Anda</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @forelse($kamars as $kamar)
                            <div @click="selectKamar({{ json_encode($kamar) }})"
                                 class="p-4 rounded-xl border-2 cursor-pointer transition flex items-center justify-between"
                                 :class="selectedKamar === {{ $kamar->id }} ? 'border-blue-600 bg-blue-50/50 ring-2 ring-blue-100' : 'border-slate-200 hover:border-slate-300 bg-white'">
                                <div class="flex items-center space-x-3">
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                        @if($kamar->foto)
                                            <img src="{{ asset('storage/' . $kamar->foto) }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-slate-300 flex items-center justify-center text-slate-500 font-bold text-xs">
                                                KMR
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-bold text-slate-900">Kamar {{ $kamar->nomor_kamar }} <span class="text-slate-400 font-normal">• Blok {{ $kamar->blok }}</span></h3>
                                        <p class="text-[11px] text-blue-600 font-semibold mt-0.5">
                                            Rp {{ number_format($kamar->harga_bulanan, 0, ',', '.') }}/bln <span class="text-slate-400 font-normal">• {{ $kamar->kapasitas - $kamar->terisi }} kasur tersisa</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="w-5 h-5 rounded-full border border-slate-300 flex items-center justify-center shrink-0"
                                     :class="selectedKamar === {{ $kamar->id }} ? 'border-blue-600 bg-blue-600 text-white' : ''">
                                    <svg x-show="selectedKamar === {{ $kamar->id }}" class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-8 text-center text-slate-400 text-xs">
                                Tidak ada kamar tersedia saat ini.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- LANGKAH 2: UNGGAH KTP -->
                <div x-show="step === 2" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Langkah 2: Unggah Kartu Tanda Penduduk (KTP)</h2>
                    <div class="max-w-md mx-auto space-y-3 text-center">
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 bg-slate-50 relative flex flex-col items-center justify-center">
                            <input type="file" name="foto_ktp" @change="handleKtpUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <template x-if="!ktpPreview">
                                <div class="space-y-2">
                                    <svg class="w-10 h-10 text-slate-400 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                    <p class="text-xs font-semibold text-slate-700">Klik atau seret file KTP ke sini</p>
                                    <p class="text-[10px] text-slate-400">Format JPG, PNG (Maksimal 2MB)</p>
                                </div>
                            </template>
                            <template x-if="ktpPreview">
                                <img :src="ktpPreview" class="max-h-48 rounded-lg shadow-sm">
                            </template>
                        </div>
                    </div>
                </div>

                <!-- LANGKAH 3: UNGGAH KTM -->
                <div x-show="step === 3" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Langkah 3: Unggah Kartu Tanda Mahasiswa (KTM)</h2>
                    <div class="max-w-md mx-auto space-y-3 text-center">
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-6 bg-slate-50 relative flex flex-col items-center justify-center">
                            <input type="file" name="foto_ktm" @change="handleKtmUpload" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <template x-if="!ktmPreview">
                                <div class="space-y-2">
                                    <svg class="w-10 h-10 text-slate-400 mx-auto" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"/></svg>
                                    <p class="text-xs font-semibold text-slate-700">Klik atau seret file KTM ke sini</p>
                                    <p class="text-[10px] text-slate-400">Format JPG, PNG (Maksimal 2MB)</p>
                                </div>
                            </template>
                            <template x-if="ktmPreview">
                                <img :src="ktmPreview" class="max-h-48 rounded-lg shadow-sm">
                            </template>
                        </div>
                    </div>
                </div>

                <!-- LANGKAH 4: TINJAU DATA -->
                <div x-show="step === 4" class="space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-2">Langkah 4: Tinjau Pengajuan Anda</h2>
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-3 text-xs">
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Nama Pemohon:</span>
                            <span class="font-bold text-slate-800">{{ auth()->user()->nama }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">NIM:</span>
                            <span class="font-bold text-slate-800">{{ auth()->user()->nim_nik }}</span>
                        </div>
                        <div class="flex justify-between border-b border-slate-200 pb-2">
                            <span class="text-slate-500">Kamar Dipilih:</span>
                            <span class="font-bold text-blue-600" x-text="'Kamar ' + kamarData.nomor_kamar + ' (Blok ' + kamarData.blok + ')'"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500">Biaya Sewa Bulanan:</span>
                            <span class="font-bold text-slate-800" x-text="'Rp ' + Number(kamarData.harga_bulanan).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

                <!-- LANGKAH 5: KONFIRMASI KIRIM -->
                <div x-show="step === 5" class="space-y-4 text-center py-4">
                    <div class="w-12 h-12 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mx-auto">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h2 class="text-base font-bold text-slate-900">Siap Mengirimkan Pengajuan?</h2>
                    <p class="text-xs text-slate-500 max-w-sm mx-auto">
                        Pastikan seluruh data yang Anda masukkan sudah benar. Pengajuan akan ditinjau oleh Admin Asrama.
                    </p>
                </div>

                <!-- KONTROL TOMBOL NAVIGASI -->
                <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                    <button type="button" @click="step--" x-show="step > 1" class="px-5 py-2.5 border border-slate-300 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                        &larr; Kembali
                    </button>
                    <div x-show="step === 1"></div>

                    <button type="button" @click="step++" x-show="step < 5" :disabled="!selectedKamar" 
                            class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                        Lanjutkan &rarr;
                    </button>

                    <button type="submit" x-show="step === 5" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition">
                        Kirim Pengajuan Reservasi
                    </button>
                </div>
            </form>

        </div>
    @endif

</x-layouts.penghuni>