<x-layouts.admin 
    title="Detail Penghuni - Admin Asrama INSTIKI" 
    activeMenu="penghuni">

    <!-- Header & Action Back -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center space-x-2">
                <a href="{{ route('admin.penghuni.index') }}" class="text-slate-400 hover:text-slate-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                </a>
                <h1 class="text-2xl font-bold text-slate-900">Detail Penghuni Aktif</h1>
            </div>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">Rincian informasi biodata dan penempatan kamar mahasiswa.</p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="px-3 py-1.5 text-xs font-bold bg-emerald-100 text-emerald-800 rounded-full inline-flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                Penghuni Aktif
            </span>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <!-- Kolom Kiri: Profil & Informasi Utama -->
        <div class="space-y-6">
            
            <!-- Card Profil Penghuni -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm text-center space-y-4">
                <div class="w-20 h-20 rounded-full bg-red-100 text-[#ed1c24] font-bold text-2xl flex items-center justify-center mx-auto border-2 border-red-200">
                    {{ strtoupper(substr($reservasi->user->nama ?? 'U', 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-900">{{ $reservasi->user->nama ?? '-' }}</h2>
                    <p class="text-xs font-mono text-slate-500 mt-0.5">{{ $reservasi->user->nim_nik ?? '-' }}</p>
                </div>
                <div class="pt-3 border-t border-slate-100 text-xs text-slate-600 space-y-2 text-left">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-medium text-slate-800">{{ $reservasi->user->email ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">No. Telepon:</span>
                        <span class="font-medium text-slate-800">{{ $reservasi->user->no_telepon ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Program Studi:</span>
                        <span class="font-medium text-slate-800">Informatika</span>
                    </div>
                </div>
            </div>

            <!-- Card Informasi Kamar -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Penempatan Kamar</h3>
                
                @if($reservasi->kamar)
                    <div class="p-4 bg-slate-50 rounded-lg border border-slate-200 space-y-2">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-bold text-slate-900">Kamar {{ $reservasi->kamar->nomor_kamar }}</span>
                            <span class="px-2.5 py-0.5 text-[10px] font-bold bg-white text-slate-800 border border-slate-200 rounded-full uppercase">
                                {{ $reservasi->kamar->kategori }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500">Blok {{ $reservasi->kamar->blok }} • Lantai {{ $reservasi->kamar->lantai }}</p>
                        <p class="text-xs font-semibold text-[#ed1c24] pt-1">
                            Rp {{ number_format($reservasi->kamar->harga_bulanan, 0, ',', '.') }} / bulan
                        </p>
                    </div>
                @else
                    <div class="p-4 bg-amber-50 rounded-lg border border-amber-200 text-amber-800 text-xs italic">
                        Kamar belum diplot oleh administrator.
                    </div>
                @endif
            </div>

        </div>

        <!-- Kolom Kanan: Detail Reservasi & Riwayat Kontrak -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Detail Reservasi / Kontrak -->
            <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Informasi Kontrak & Pengajuan</h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-1">
                        <span class="text-slate-400 block">Kode Reservasi</span>
                        <span class="font-mono font-bold text-slate-800 text-sm">{{ $reservasi->kode_reservasi }}</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-1">
                        <span class="text-slate-400 block">Tanggal Pengajuan</span>
                        <span class="font-bold text-slate-800 text-sm">{{ \Carbon\Carbon::parse($reservasi->tanggal_pengajuan)->format('d F Y') }}</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-1">
                        <span class="text-slate-400 block">Durasi Sewa</span>
                        <span class="font-bold text-slate-800 text-sm">{{ $reservasi->durasi_sewa }}</span>
                    </div>

                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-100 space-y-1">
                        <span class="text-slate-400 block">Batas Maksimal Tinggal</span>
                        <span class="font-bold text-amber-600 text-sm">2 Semester (1 Tahun)</span>
                    </div>
                </div>
            </div>

            <!-- Catatan Admin -->
            @if($reservasi->catatan_admin)
                <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-2">
                    <h3 class="text-sm font-bold text-slate-900">Catatan Admin</h3>
                    <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-lg border border-slate-100">
                        {{ $reservasi->catatan_admin }}
                    </p>
                </div>
            @endif

            <!-- Tombol Navigasi Bawah -->
            <div class="flex justify-end space-x-3 pt-4 border-t border-slate-200">
                <a href="{{ route('admin.penghuni.index') }}" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 font-semibold text-xs rounded-lg hover:bg-slate-50 transition">
                    Kembali ke Daftar
                </a>
            </div>

        </div>

    </div>

</x-layouts.admin>