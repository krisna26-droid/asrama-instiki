<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Kamar - Admin Asrama INSTIKI</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" x-data="{ showAddModal: false, showEditModal: false, editData: {} }">
    <div class="min-h-screen flex">

        <!-- Sidebar Kiri -->
        <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between shrink-0 hidden md:flex">
            <div>
                <div class="h-20 flex items-center px-6 border-b border-slate-100">
                    <img src="{{ asset('image/instiki-logo.png') }}" alt="Logo INSTIKI" class="h-9 w-auto">
                    <div class="ml-3 border-l border-slate-200 pl-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-800 block">Asrama</span>
                        <span class="text-[10px] text-slate-500">INSTIKI</span>
                    </div>
                </div>

                <div class="px-4 py-6">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 px-3 block mb-3">Menu Utama</span>
                    <nav class="space-y-1">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Dashboard
                        </a>
                        <a href="{{ route('kamar.index') }}" class="flex items-center px-3 py-2.5 text-sm font-semibold rounded-lg bg-red-50 text-[#ed1c24]">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Manajemen Kamar
                        </a>
                        <a href="{{ route('admin.reservasi.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Verifikasi Reservasi
                        </a>
                        <a href="{{ route('admin.penghuni.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-slate-600 rounded-lg hover:bg-slate-100 transition">
                            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                            Penghuni Aktif
                        </a>
                    </nav>
                </div>
            </div>

            <div class="p-4 border-t border-slate-200">
                <div class="bg-slate-50 p-3 rounded-lg">
                    <p class="text-xs font-bold text-slate-800">Admin Asrama</p>
                    <p class="text-[10px] text-slate-500">v1.0 Operational</p>
                </div>
            </div>
        </aside>

        <!-- Area Utama -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Navbar -->
            <header class="h-20 bg-white border-b border-slate-200 px-6 flex items-center justify-between sticky top-0 z-10">
                <div class="w-72 sm:w-96">
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </span>
                        <input type="text" placeholder="Cari kamar..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-3 text-right">
                        <div>
                            <span class="block text-sm font-bold text-slate-800">{{ auth()->user()->nama }}</span>
                            <span class="block text-xs text-slate-500">{{ auth()->user()->email }}</span>
                        </div>
                        <div class="w-9 h-9 rounded-full bg-red-100 text-[#ed1c24] font-bold flex items-center justify-center text-sm border border-red-200">
                            {{ substr(auth()->user()->nama, 0, 1) }}
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="p-6 sm:p-8 space-y-6 flex-1 overflow-y-auto">

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
                                <img src="{{ $kamar->foto }}" alt="Foto Kamar" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
                                
                                <!-- Status Badge -->
                                <div class="absolute top-3 left-3">
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
                                    <span class="text-slate-500 font-medium">Terisi: <strong>{{ $kamar->terisi }}/{{ $kamar->kapasitas }} Orang</strong></span>
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
                                <button @click="showEditModal = true; editData = {{ json_encode($kamar) }}" class="flex-1 py-2 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-100 transition text-center">
                                    Edit Kamar
                                </button>
                                <form action="{{ route('kamar.destroy', $kamar->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kamar ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-[#ed1c24] transition" title="Hapus">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>

                        </div>
                    @empty
                        <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-xl border border-slate-200">
                            <p class="text-sm">Belum ada data kamar.</p>
                        </div>
                    @endforelse
                </div>

            </main>
        </div>

    </div>

    <!-- MODAL TAMBAH KAMAR -->
    <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6 space-y-4">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h3 class="text-lg font-bold text-slate-900">Tambah Kamar Baru</h3>
                <button @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
            </div>

            <form action="{{ route('kamar.store') }}" method="POST" class="space-y-4 text-xs">
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
                        <input type="number" name="harga_bulanan" value="850000" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
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

            <form :action="'/admin/kamar/' + editData.id" method="POST" class="space-y-4 text-xs">
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
                    <input type="url" name="foto" x-model="editData.foto" class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showEditModal = false" class="px-4 py-2 border border-slate-300 rounded font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-[#ed1c24] text-white font-semibold rounded hover:bg-red-700">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>