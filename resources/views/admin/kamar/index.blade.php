<x-layouts.admin 
    title="Manajemen Kamar - Admin Asrama INSTIKI" 
    activeMenu="kamar"
    x-data="{ showAddModal: false, showEditModal: false, editData: {} }">

    <!-- Title & Action Button -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Manajemen Kamar</h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola data kamar asrama, kapasitas, dan fasilitas.</p>
        </div>
        <button @click="showAddModal = true" class="px-5 py-2.5 bg-[#ed1c24] text-white font-semibold text-sm rounded-lg hover:bg-red-700 transition flex items-center justify-center shadow-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"></path></svg>
            Tambah Kamar
        </button>
    </div>

    <!-- Alert Flash Success -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @foreach($errors->all() as $error)<div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>@endforeach

    <form method="GET" action="{{ route('kamar.index') }}" class="grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-4 sm:grid-cols-2 lg:grid-cols-6">
        <input name="search" value="{{ request('search') }}" placeholder="Nomor kamar / blok" class="rounded-lg border-slate-300">
        <select name="kategori" class="rounded-lg border-slate-300"><option value="">Semua kategori</option><option value="putra" @selected(request('kategori')==='putra')>Putra</option><option value="putri" @selected(request('kategori')==='putri')>Putri</option></select>
        <select name="status" class="rounded-lg border-slate-300"><option value="">Semua kondisi</option><option value="tersedia" @selected(request('status')==='tersedia')>Tersedia</option><option value="tersewa_penuh" @selected(request('status')==='tersewa_penuh')>Penuh (legacy)</option><option value="perbaikan" @selected(request('status')==='perbaikan')>Perbaikan</option></select>
        <input type="number" min="1" name="kapasitas_min" value="{{ request('kapasitas_min') }}" placeholder="Kapasitas minimum" class="rounded-lg border-slate-300">
        <select name="aktif" class="rounded-lg border-slate-300"><option value="">Semua status arsip</option><option value="1" @selected(request('aktif')==='1')>Aktif</option><option value="0" @selected(request('aktif')==='0')>Diarsipkan</option></select>
        <div class="flex gap-2"><button class="flex-1 rounded-lg bg-slate-800 px-3 py-2 text-white">Filter</button><a href="{{ route('kamar.index') }}" class="rounded-lg border px-3 py-2 text-slate-700">Reset</a></div>
    </form>

    <!-- Filter Status -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
        <a href="{{ route('kamar.index', ['status' => 'semua']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status', 'semua') == 'semua' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Semua</a>
        <a href="{{ route('kamar.index', ['status' => 'tersedia']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status') == 'tersedia' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Tersedia</a>
        <a href="{{ route('kamar.index', ['status' => 'tersewa_penuh']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status') == 'tersewa_penuh' ? 'bg-slate-700 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Penuh</a>
        <a href="{{ route('kamar.index', ['status' => 'perbaikan']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status') == 'perbaikan' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Perbaikan</a>
    </div>

    <!-- Grid Card Kamar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($kamars as $kamar)
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-md transition flex flex-col justify-between">
                
                <!-- Image Header & Status Badge -->
                <div class="relative h-44 bg-slate-100">
                    @if($kamar->foto)
                        @php
                            $fotoKamarUrl = \Illuminate\Support\Str::startsWith($kamar->foto, ['http://', 'https://'])
                                ? $kamar->foto
                                : url('storage/' . ltrim($kamar->foto, '/'));
                        @endphp
                        <img src="{{ $fotoKamarUrl }}" alt="Foto Kamar {{ $kamar->nomor_kamar }}" class="w-full h-full object-cover">
                    @else
                        <div class="flex h-full items-center justify-center text-sm text-slate-400">Foto kamar belum tersedia</div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                    
                    <!-- Status Badge -->
                    <div class="absolute top-3 left-3 flex flex-wrap gap-1">
                        @if(!$kamar->is_active)<span class="px-2.5 py-1 text-[11px] font-bold bg-slate-900 text-white rounded-full">Diarsipkan</span>@endif
                        @if($kamar->status === 'tersedia')
                            <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-500 text-white rounded-full">Tersedia</span>
                        @elseif($kamar->status === 'tersewa_penuh')
                            <span class="px-2.5 py-1 text-[11px] font-bold bg-slate-800 text-white rounded-full">Penuh</span>
                        @else
                            <span class="px-2.5 py-1 text-[11px] font-bold bg-amber-500 text-white rounded-full">Perbaikan</span>
                        @endif

                        <span class="px-2.5 py-1 text-[11px] font-bold bg-white/90 text-slate-900 rounded-full ml-1 uppercase">
                            {{ $kamar->kategori }}
                        </span>
                    </div>

                    <!-- Text overlay -->
                    <div class="absolute bottom-3 left-3 right-3 text-white">
                        <h3 class="text-lg font-bold">Kamar {{ $kamar->nomor_kamar }}</h3>
                        <p class="text-xs text-slate-300">Blok {{ $kamar->blok }} • Lantai {{ $kamar->lantai }}</p>
                    </div>
                </div>

                <!-- Body Detail -->
                <div class="p-5 space-y-4 flex-1">
                    <div class="flex justify-between items-center text-xs border-b border-slate-100 pb-3">
                        <span class="text-slate-500 font-medium">Penghuni aktif: <strong>{{ $kamar->penghuni_aktif_count }}/{{ $kamar->kapasitas }} orang</strong></span>
                        <span class="font-bold text-[#ed1c24]">Rp {{ number_format($kamar->harga_bulanan, 0, ',', '.') }} <span class="text-[10px] text-slate-400 font-normal">/bulan</span></span>
                    </div>

                    <!-- List Fasilitas (Tag Pill) -->
                    <div class="flex flex-wrap gap-1.5">
                        @if($kamar->fasilitas)
                            @foreach(explode(',', $kamar->fasilitas) as $item)
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[11px] font-medium">{{ trim($item) }}</span>
                            @endforeach
                        @else
                            <span class="text-xs text-slate-400 italic">Tidak ada rincian fasilitas.</span>
                        @endif
                    </div>
                </div>

                <!-- Card Footer Action -->
                <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-3">
                    <button @click="showEditModal = true; editData = {{ \Illuminate\Support\Js::from($kamar) }}; editData.fotoInput = {{ \Illuminate\Support\Js::from(\Illuminate\Support\Str::startsWith($kamar->foto ?? '', ['http://', 'https://']) ? $kamar->foto : '') }}" class="flex-1 py-2 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-100 transition text-center">
                        Edit Kamar
                    </button>
                    <form action="{{ route('kamar.destroy', $kamar->id) }}" method="POST" onsubmit="return confirm('Arsipkan kamar ini? Kamar dengan penempatan yang belum selesai tidak dapat dinonaktifkan.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-2 text-slate-400 hover:text-[#ed1c24] transition" title="Arsipkan">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                    <form action="{{ route('admin.kamar.toggle-active', $kamar) }}" method="POST" onsubmit="return confirm('Ubah status aktif kamar ini?')">@csrf @method('PATCH')<button class="rounded border px-3 py-2 text-xs font-semibold">{{ $kamar->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button></form>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                <p class="text-sm">Belum ada data kamar.</p>
            </div>
        @endforelse
    </div>
    <div>{{ $kamars->links() }}</div>

    <!-- MODAL TAMBAH KAMAR -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Tambah Kamar Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('kamar.store') }}" method="POST" enctype="multipart/form-data" x-data="{ selectedRoomPhoto: null }" class="space-y-4 text-xs">
                @csrf
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor Kamar</label>
                        <input type="text" name="nomor_kamar" placeholder="Contoh: 101" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Blok</label>
                        <input type="text" name="blok" placeholder="A / B" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Lantai</label>
                        <input type="number" name="lantai" value="1" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kategori Penghuni</label>
                        <select name="kategori" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                            <option value="putra">Putra</option>
                            <option value="putri">Putri</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" value="3" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Harga Bulanan (Rp)</label>
                        <input type="number" name="harga_bulanan" value="{{ old('harga_bulanan') }}" min="0" required class="w-full border-slate-300 rounded text-xs focus:border-blue-600 focus:ring-blue-600">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Kamar</label>
                    <select name="status" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                        <option value="tersedia">Tersedia</option>
                        <option value="tersewa_penuh">Penuh</option>
                        <option value="perbaikan">Dalam Perbaikan</option>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Fasilitas (pisahkan dengan koma)</label>
                    <input type="text" name="fasilitas" placeholder="AC, WiFi, Meja Belajar, Lemari" class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">URL Foto Kamar (Opsional)</label>
                    <input type="url" name="foto" placeholder="https://..." class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                </div>

                <div>
                    <label for="foto_file_tambah" class="font-bold text-slate-700 block mb-1">Atau unggah foto</label>
                    <input id="foto_file_tambah" type="file" name="foto_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @change="selectedRoomPhoto = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="block w-full rounded border border-slate-300 p-2 text-xs">
                    <p class="mt-1 text-[11px] text-slate-500">JPG, PNG, atau WebP; maksimal 5 MB. Jika URL dan file diisi, file unggahan digunakan.</p>
                    <img x-show="selectedRoomPhoto" :src="selectedRoomPhoto" alt="Pratinjau foto kamar" class="mt-3 h-36 w-full rounded-lg border border-slate-200 object-cover" x-cloak>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showAddModal = false" class="px-4 py-2 border border-slate-300 rounded font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#ed1c24] text-white font-semibold rounded hover:bg-red-700">Simpan Kamar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL EDIT KAMAR -->
    <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Edit Kamar</h3>
                <button @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form :action="'/admin/kamar/' + editData.id" method="POST" enctype="multipart/form-data" x-data="{ selectedRoomPhoto: null }" class="space-y-4 text-xs">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Nomor Kamar</label>
                        <input type="text" name="nomor_kamar" x-model="editData.nomor_kamar" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Blok</label>
                        <input type="text" name="blok" x-model="editData.blok" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Lantai</label>
                        <input type="number" name="lantai" x-model="editData.lantai" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kategori Penghuni</label>
                        <select name="kategori" x-model="editData.kategori" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                            <option value="putra">Putra</option>
                            <option value="putri">Putri</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Kapasitas (Orang)</label>
                        <input type="number" name="kapasitas" x-model="editData.kapasitas" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                    <div>
                        <label class="font-bold text-slate-700 block mb-1">Harga Bulanan (Rp)</label>
                        <input type="number" name="harga_bulanan" x-model="editData.harga_bulanan" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                    </div>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Status Kamar</label>
                    <select name="status" x-model="editData.status" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                        <option value="tersedia">Tersedia</option>
                        <option value="tersewa_penuh">Penuh</option>
                        <option value="perbaikan">Dalam Perbaikan</option>
                    </select>
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Fasilitas (pisahkan dengan koma)</label>
                    <input type="text" name="fasilitas" x-model="editData.fasilitas" class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                </div>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">URL Foto Kamar</label>
                    <input type="url" name="foto" x-model="editData.fotoInput" placeholder="https://..." class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                </div>

                <div>
                    <label for="foto_file_edit" class="font-bold text-slate-700 block mb-1">Atau unggah foto baru</label>
                    <input id="foto_file_edit" type="file" name="foto_file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" @change="selectedRoomPhoto = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null" class="block w-full rounded border border-slate-300 p-2 text-xs">
                    <p class="mt-1 text-[11px] text-slate-500">JPG, PNG, atau WebP; maksimal 5 MB. Kosongkan URL dan file untuk mempertahankan foto saat ini.</p>
                    <img x-show="selectedRoomPhoto" :src="selectedRoomPhoto" alt="Pratinjau foto kamar baru" class="mt-3 h-36 w-full rounded-lg border border-slate-200 object-cover" x-cloak>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-slate-300 rounded font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#ed1c24] text-white font-semibold rounded hover:bg-red-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>
