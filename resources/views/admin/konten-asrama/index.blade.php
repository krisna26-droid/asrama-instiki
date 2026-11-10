<x-layouts.admin title="Konten Asrama - Admin Asrama INSTIKI" activeMenu="konten-asrama">

    @php
        $inputClass = 'w-full rounded-lg border-slate-300 text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24]';
        $labelClass = 'mb-1 block text-xs font-semibold text-slate-700';
        $fileClass  = 'block w-full rounded-lg border border-slate-300 p-2 text-sm file:mr-3 file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-2 file:text-xs file:font-semibold';
        $accept     = '.jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp';
    @endphp

    <!-- Heading + tombol tambah -->
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <span class="text-[11px] font-bold uppercase tracking-widest text-[#ed1c24]">Halaman publik</span>
            <h1 class="mt-1 text-2xl font-bold text-slate-900">Informasi Asrama</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Kelola foto fasilitas, keterangan, dan kontak yang tampil kepada pengunjung.</p>
        </div>

        {{-- Pop up: Tambah foto fasilitas (terbuka lagi otomatis kalau validasi gagal) --}}
        <x-admin.modal title="Tambah Foto Fasilitas" maxWidth="40rem" :open="$errors->any() && old('judul') !== null">
            <x-slot name="trigger">
                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Tambah Foto
                </button>
            </x-slot>

            <form method="POST" action="{{ route('admin.konten-asrama.fasilitas.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Judul</label>
                        <input name="judul" value="{{ old('judul') }}" required maxlength="150" placeholder="Contoh: Area kamar mandi" class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Urutan tampil</label>
                        <input type="number" name="urutan" value="{{ old('urutan', 0) }}" min="0" max="9999" class="{{ $inputClass }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}">Caption singkat</label>
                        <input name="caption" value="{{ old('caption') }}" required maxlength="255" placeholder="Kalimat ringkas untuk kolase" class="{{ $inputClass }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}">Deskripsi detail</label>
                        <textarea name="deskripsi" rows="3" placeholder="Informasi lebih lengkap untuk halaman detail" class="{{ $inputClass }}">{{ old('deskripsi') }}</textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="{{ $labelClass }}">Foto fasilitas</label>
                        <input type="file" name="gambar" required accept="{{ $accept }}" class="{{ $fileClass }}">
                        <span class="mt-1 block text-[11px] text-slate-500">JPG, PNG, atau WebP; maksimal 5 MB.</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                    <button class="rounded-lg bg-[#ed1c24] px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Tambah ke Halaman Publik</button>
                </div>
            </form>
        </x-admin.modal>
    </div>

    <!-- Notifikasi -->
    @if(session('success'))
        <div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif
    @foreach($errors->all() as $error)
        <div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    <!-- Kontak asrama -->
    <section class="rounded-lg border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
        <div class="mb-5 border-b border-slate-100 pb-4">
            <h2 class="text-base font-bold text-slate-900">Kontak Asrama</h2>
            <p class="mt-1 text-xs text-slate-500">Kontak yang diisi akan ditampilkan pada section informasi di halaman guest.</p>
        </div>

        <form method="POST" action="{{ route('admin.konten-asrama.contact.update') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            @csrf
            @method('PUT')

            <div>
                <label class="{{ $labelClass }}">WhatsApp</label>
                <input name="whatsapp" value="{{ old('whatsapp', $contact->whatsapp) }}" placeholder="Contoh: +62 8xx xxxx xxxx" class="{{ $inputClass }}">
            </div>
            <div>
                <label class="{{ $labelClass }}">Telepon</label>
                <input name="telepon" value="{{ old('telepon', $contact->telepon) }}" placeholder="Nomor telepon asrama" class="{{ $inputClass }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Email</label>
                <input type="email" name="email" value="{{ old('email', $contact->email) }}" placeholder="Email kontak asrama" class="{{ $inputClass }}">
            </div>
            <div class="sm:col-span-2">
                <label class="{{ $labelClass }}">Alamat</label>
                <textarea name="alamat" rows="3" placeholder="Alamat yang boleh ditampilkan untuk publik" class="{{ $inputClass }}">{{ old('alamat', $contact->alamat) }}</textarea>
            </div>
            <div class="flex justify-end sm:col-span-2">
                <button class="rounded-lg bg-slate-800 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-900">Simpan Kontak</button>
            </div>
        </form>
    </section>

    <!-- Galeri fasilitas -->
    <section class="space-y-4">
        <div>
            <h2 class="text-base font-bold text-slate-900">Galeri Fasilitas</h2>
            <p class="mt-1 text-xs text-slate-500">Fasilitas nonaktif tetap tersimpan sebagai arsip dan tidak muncul untuk publik.</p>
        </div>

        @if($facilities->isNotEmpty())
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($facilities as $facility)
                    <article class="flex flex-col overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
                        <img src="{{ '/storage/' . ltrim($facility->gambar_path, '/') }}" alt="{{ $facility->judul }}" class="w-full object-cover" style="height: 10rem;">

                        <div class="flex flex-1 flex-col p-4">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="min-w-0 truncate text-sm font-bold text-slate-900">{{ $facility->judul }}</h3>
                                <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-bold {{ $facility->aktif ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $facility->aktif ? 'Tampil' : 'Arsip' }}</span>
                            </div>
                            <p class="mt-1 line-clamp-2 text-xs text-slate-500">{{ $facility->caption }}</p>
                            <p class="mt-2 text-[11px] text-slate-400">Urutan tampil: {{ $facility->urutan }}</p>

                            <div class="mt-4 flex flex-wrap items-center gap-2 border-t border-slate-100 pt-4">

                                {{-- Pop up: Edit --}}
                                <x-admin.modal title="Edit Fasilitas" maxWidth="40rem">
                                    <x-slot name="trigger">
                                        <button type="button" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit</button>
                                    </x-slot>

                                    <form method="POST" action="{{ route('admin.konten-asrama.fasilitas.update', $facility) }}" enctype="multipart/form-data" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                            <div>
                                                <label class="{{ $labelClass }}">Judul</label>
                                                <input name="judul" value="{{ $facility->judul }}" required maxlength="150" class="{{ $inputClass }}">
                                            </div>
                                            <div>
                                                <label class="{{ $labelClass }}">Urutan tampil</label>
                                                <input type="number" name="urutan" value="{{ $facility->urutan }}" min="0" max="9999" class="{{ $inputClass }}">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="{{ $labelClass }}">Caption</label>
                                                <input name="caption" value="{{ $facility->caption }}" required maxlength="255" class="{{ $inputClass }}">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="{{ $labelClass }}">Deskripsi</label>
                                                <textarea name="deskripsi" rows="3" class="{{ $inputClass }}">{{ $facility->deskripsi }}</textarea>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="{{ $labelClass }}">Ganti foto (opsional)</label>
                                                <input type="file" name="gambar" accept="{{ $accept }}" class="{{ $fileClass }}">
                                                <span class="mt-1 block text-[11px] text-slate-500">Kosongkan jika ingin mempertahankan foto saat ini. Maksimal 5 MB.</span>
                                            </div>
                                        </div>

                                        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                            <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                            <button class="rounded-lg bg-[#ed1c24] px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </x-admin.modal>

                                {{-- Pop up: Arsipkan / tampilkan --}}
                                <x-admin.modal :title="$facility->aktif ? 'Arsipkan Fasilitas' : 'Tampilkan Fasilitas'">
                                    <x-slot name="trigger">
                                        <button type="button" class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $facility->aktif ? 'border-amber-300 text-amber-800 hover:bg-amber-50' : 'border-emerald-300 text-emerald-800 hover:bg-emerald-50' }}">{{ $facility->aktif ? 'Arsipkan' : 'Tampilkan' }}</button>
                                    </x-slot>

                                    <form method="POST" action="{{ route('admin.konten-asrama.fasilitas.toggle', $facility) }}" class="space-y-4">
                                        @csrf
                                        @method('PATCH')

                                        <div class="rounded-lg bg-slate-50 px-3 py-2.5">
                                            <span class="block text-sm font-semibold text-slate-900">{{ $facility->judul }}</span>
                                            <span class="block text-xs text-slate-500">{{ $facility->caption }}</span>
                                        </div>

                                        <p class="text-sm text-slate-600">
                                            {{ $facility->aktif ? 'Fasilitas ini akan disembunyikan dari halaman publik, tetapi datanya tetap tersimpan.' : 'Fasilitas ini akan tampil kembali di halaman publik.' }}
                                        </p>

                                        <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                            <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                            <button class="rounded-lg px-4 py-2 text-xs font-semibold text-white transition {{ $facility->aktif ? 'bg-amber-600 hover:bg-amber-700' : 'bg-emerald-700 hover:bg-emerald-800' }}">{{ $facility->aktif ? 'Arsipkan' : 'Tampilkan' }}</button>
                                        </div>
                                    </form>
                                </x-admin.modal>

                                @if($facility->aktif)
                                    <a href="{{ route('asrama.detail') }}" target="_blank" rel="noopener" class="ml-auto text-xs font-semibold text-[#ed1c24] hover:text-red-700 hover:underline">Lihat semua detail publik</a>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <div class="rounded-lg border border-dashed border-slate-300 bg-white p-8 text-center">
                <p class="text-sm font-semibold text-slate-800">Belum ada foto fasilitas</p>
                <p class="mt-1 text-xs text-slate-500">Tambahkan foto pertama untuk mengisi kolase di halaman guest.</p>
            </div>
        @endif
    </section>
</x-layouts.admin>
