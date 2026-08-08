<x-layouts.penghuni 
    title="Dashboard Penghuni - Asrama INSTIKI" 
    activeMenu="dashboard">

    <!-- Hero Banner Utama -->
    <div class="rounded-2xl p-6 sm:p-8 text-white shadow-sm relative overflow-hidden bg-blue-600" style="background-color: #2563eb !important;">
        <div class="relative z-10 space-y-3 max-w-xl">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-xs font-semibold text-white border border-white/30">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                Selamat datang kembali
            </span>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-white">Halo, {{ auth()->user()->nama }}</h1>
            <p class="text-xs sm:text-sm text-blue-100 leading-relaxed">
                Kelola reservasi kamar asrama, pembayaran sewa bulanan, dan profil Anda dalam satu tempat terpadu.
            </p>

            <div class="flex flex-wrap gap-3 pt-2">
                @if(!$reservasi || $reservasi->status === 'rejected')
                    <a href="{{ route('penghuni.kamar.index') }}" class="px-4 py-2.5 bg-white text-blue-700 font-bold text-xs rounded-xl shadow-sm hover:bg-blue-50 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                        Pesan Kamar
                    </a>
                @endif

                @if($reservasi && $reservasi->status === 'approved')
                    <a href="{{ route('penghuni.pembayaran.index') }}" class="px-4 py-2.5 bg-blue-700 border border-blue-400 text-white font-bold text-xs rounded-xl hover:bg-blue-800 transition inline-flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        Bayar Sewa
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- 3 Ringkasan Kartu Status (Secara Samping / Grid 3 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Kartu 1: Status Reservasi -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Status Reservasi</p>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">
                        @if(!$reservasi)
                            Belum Ada
                        @elseif($reservasi->status === 'approved')
                            Disetujui
                        @elseif($reservasi->status === 'pending')
                            Menunggu
                        @else
                            Ditolak
                        @endif
                    </h3>
                </div>
                <div class="w-9 h-9 rounded-full {{ !$reservasi ? 'bg-slate-100 text-slate-400' : ($reservasi->status === 'approved' ? 'bg-emerald-50 text-emerald-600' : ($reservasi->status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')) }} flex items-center justify-center border shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4">
                @if($reservasi && $reservasi->kamar)
                    Kamar {{ $reservasi->kamar->nomor_kamar }} • Blok {{ $reservasi->kamar->blok }}
                @else
                    Silakan lakukan pengajuan kamar
                @endif
            </p>
        </div>

        <!-- Kartu 2: Status Pembayaran -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Status Pembayaran</p>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">
                        @if(!$pembayaranTerakhir)
                            Belum Ada
                        @elseif($pembayaranTerakhir->status === 'paid')
                            Lunas
                        @elseif($pembayaranTerakhir->status === 'pending')
                            Verifikasi
                        @else
                            Ditolak
                        @endif
                    </h3>
                </div>
                <div class="w-9 h-9 rounded-full {{ !$pembayaranTerakhir ? 'bg-slate-100 text-slate-400' : ($pembayaranTerakhir->status === 'paid' ? 'bg-emerald-50 text-emerald-600' : ($pembayaranTerakhir->status === 'pending' ? 'bg-amber-50 text-amber-600' : 'bg-rose-50 text-rose-600')) }} flex items-center justify-center border shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4">
                @if($pembayaranTerakhir)
                    {{ \Carbon\Carbon::parse($pembayaranTerakhir->created_at)->format('F Y') }}
                @else
                    Tidak ada tagihan aktif
                @endif
            </p>
        </div>

        <!-- Kartu 3: Kamar Penempatan -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-slate-500">Kamar Penempatan</p>
                    <h3 class="text-xl font-bold text-slate-900 mt-2">
                        @if($reservasi && $reservasi->kamar && $reservasi->status === 'approved')
                            Kamar {{ $reservasi->kamar->nomor_kamar }}
                        @else
                            Belum Ditempatkan
                        @endif
                    </h3>
                </div>
                <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100 shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h5m0 0v-5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5"/></svg>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-4">
                @if($reservasi && $reservasi->kamar && $reservasi->status === 'approved')
                    Blok {{ $reservasi->kamar->blok }} • Lantai {{ $reservasi->kamar->lantai }}
                @else
                    Menunggu konfirmasi admin
                @endif
            </p>
        </div>

    </div>

    <!-- Bagian Bawah: Grafik Riwayat Pembayaran Dinamis & Aksi Cepat -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Visual Grafik Batang Dinamis -->
        <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-slate-200 shadow-sm flex flex-col justify-between">
            <div class="flex justify-between items-center border-b border-slate-100 pb-3">
                <h2 class="text-xs font-bold text-slate-700">Riwayat Pembayaran Saya ({{ date('Y') }})</h2>
                <span class="text-[10px] text-slate-400 font-mono">Status: Lunas</span>
            </div>

            <!-- Area Batang Grafik -->
            <div class="h-48 flex items-end justify-between gap-2 pt-8 px-2 border-b border-slate-100">
                @php
                    $namaBulan = [1=>'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
                @endphp

                @foreach($chartData as $bulanIndex => $total)
                    @php
                        // Hitung persentase tinggi batang (minimal 4% agar tetap terlihat landasannya)
                        $heightPercent = $total > 0 ? round(($total / $maxAmount) * 100) : 4;
                    @endphp
                    <div class="flex-1 flex flex-col items-center gap-2 h-full justify-end group relative">
                        <!-- Tooltip saat hover -->
                        <div class="opacity-0 group-hover:opacity-100 transition absolute -top-8 bg-slate-800 text-white text-[10px] py-1 px-2 rounded font-mono pointer-events-none z-20 whitespace-nowrap">
                            Rp {{ number_format($total, 0, ',', '.') }}
                        </div>

                        <!-- Batang Grafik -->
                        <div class="w-full rounded-t-md transition-all duration-300 {{ $total > 0 ? 'bg-blue-600 hover:bg-blue-700' : 'bg-slate-100' }}"
                             style="height: {{ $heightPercent }}%;">
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Label Bulan -->
            <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 px-2 font-mono">
                @foreach($namaBulan as $m)
                    <span class="flex-1 text-center">{{ $m }}</span>
                @endforeach
            </div>
        </div>

        <!-- Aksi Cepat -->
        <div class="bg-white p-6 rounded-xl border border-slate-200 shadow-sm space-y-4">
            <h2 class="text-xs font-bold text-slate-700">Aksi Cepat</h2>

            <div class="space-y-2.5">
                <a href="{{ route('penghuni.kamar.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl flex items-center justify-between group transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700">Lihat kamar tersedia</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('penghuni.reservasi.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl flex items-center justify-between group transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700">Mulai reservasi baru</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('penghuni.pembayaran.index') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl flex items-center justify-between group transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0l-4 4m4-4v12"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700">Unggah bukti pembayaran</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>

                <a href="{{ route('profile.edit') }}" class="p-3 bg-slate-50 hover:bg-slate-100 border border-slate-100 rounded-xl flex items-center justify-between group transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        </div>
                        <span class="text-xs font-semibold text-slate-700">Perbarui profil</span>
                    </div>
                    <svg class="w-4 h-4 text-slate-400 group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
        </div>

    </div>

</x-layouts.penghuni>