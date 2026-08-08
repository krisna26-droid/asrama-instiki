<x-layouts.penghuni 
    title="Kamar Tersedia - Asrama INSTIKI" 
    activeMenu="kamar">

    @php
        $hasActiveRoom = auth()->user()->punyaKamarAktif();
    @endphp

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Kamar Tersedia</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Jelajahi dan pilih kamar asrama sesuai kebutuhan Anda</p>
    </div>

    @if($hasActiveRoom)
        <!-- Peringatan Pembatasan 1 Kamar -->
        <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3 text-amber-800 text-xs sm:text-sm">
            <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>Anda sudah memiliki reservasi/kamar aktif. Satu penghuni hanya diperbolehkan mendaftar pada 1 kamar.</span>
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <form action="{{ route('penghuni.kamar.index') }}" method="GET" class="w-full md:w-96">
            <input type="hidden" name="filter" value="{{ request('filter', 'tersedia') }}">
            <div class="relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kamar atau fasilitas..." class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-blue-600 focus:ring-blue-600 transition shadow-sm">
            </div>
        </form>

        <div class="flex items-center gap-2">
            <a href="{{ route('penghuni.kamar.index', ['filter' => 'tersedia', 'search' => request('search')]) }}" 
               class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ request('filter', 'tersedia') === 'tersedia' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Tersedia Saja
            </a>
            <a href="{{ route('penghuni.kamar.index', ['filter' => 'semua', 'search' => request('search')]) }}" 
               class="px-4 py-2 text-xs font-semibold rounded-xl transition {{ request('filter') === 'semua' ? 'bg-blue-600 text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua Kamar
            </a>
        </div>
    </div>

    <!-- Grid Kartu Kamar -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($kamars as $kamar)
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition">
                <div class="relative h-48 bg-slate-100 overflow-hidden">
                    @if($kamar->foto)
                        <img src="{{ \Illuminate\Support\Str::startsWith($kamar->foto, 'http') ? $kamar->foto : asset('storage/' . $kamar->foto) }}" alt="Kamar {{ $kamar->nomor_kamar }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-slate-500 flex items-center justify-center text-white">
                            <svg class="w-12 h-12 opacity-30" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                    @endif

                    <div class="absolute top-3 left-3">
                        @if($kamar->status === 'tersedia')
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-500 text-white rounded-full inline-flex items-center gap-1 shadow-sm">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                Tersedia
                            </span>
                        @elseif($kamar->status === 'tersewa_penuh')
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-slate-700 text-white rounded-full inline-flex items-center gap-1 shadow-sm">
                                Penuh
                            </span>
                        @else
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-amber-500 text-white rounded-full inline-flex items-center gap-1 shadow-sm">
                                Perbaikan
                            </span>
                        @endif
                    </div>

                    <div class="absolute bottom-0 inset-x-0 bg-slate-900/80 p-4 text-white">
                        <h3 class="text-lg font-bold">Kamar {{ $kamar->nomor_kamar }}</h3>
                        <p class="text-xs text-slate-200 mt-0.5">Blok {{ $kamar->blok }} • Lantai {{ $kamar->lantai }}</p>
                    </div>
                </div>

                <div class="p-5 space-y-4 flex-1 flex flex-col justify-between">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-600 font-medium">Tersisa {{ $kamar->kapasitas - $kamar->terisi }} dari {{ $kamar->kapasitas }} kasur</span>
                            <span class="font-bold text-blue-600 text-sm">Rp {{ number_format($kamar->harga_bulanan, 0, ',', '.') }}<span class="text-[10px] text-slate-400 font-normal">/bln</span></span>
                        </div>

                        @if($kamar->fasilitas)
                            <div class="flex flex-wrap gap-1.5">
                                @foreach(explode(',', $kamar->fasilitas) as $fasilitas)
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-medium border border-slate-200">
                                        {{ trim($fasilitas) }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="pt-3">
                        @if($hasActiveRoom)
                            <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-semibold text-xs rounded-xl text-center cursor-not-allowed">
                                Anda Sudah Memiliki Kamar
                            </button>
                        @elseif($kamar->status === 'tersedia' && ($kamar->kapasitas - $kamar->terisi) > 0)
                            <a href="{{ route('penghuni.reservasi.index') }}" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs rounded-xl transition shadow-sm text-center block">
                                Pesan Sekarang
                            </a>
                        @else
                            <button disabled class="w-full py-2.5 bg-slate-100 text-slate-400 font-semibold text-xs rounded-xl text-center cursor-not-allowed">
                                Tidak Tersedia
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-12 text-center bg-white rounded-2xl border border-slate-200 p-8 space-y-3">
                <h3 class="text-sm font-bold text-slate-800">Kamar tidak ditemukan</h3>
            </div>
        @endforelse
    </div>

</x-layouts.penghuni>