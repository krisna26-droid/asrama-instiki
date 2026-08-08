<x-layouts.keuangan 
    title="Verifikasi Pembayaran - Admin Keuangan INSTIKI" 
    activeMenu="pembayaran"
    searchRoute="{{ route('keuangan.pembayaran.index') }}"
    searchPlaceholder="Cari nama penghuni atau kode pembayaran..."
    x-data="{ showModalVerify: false, showModalReject: false, showModalProof: false, selectedPayment: {}, proofUrl: '' }">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Verifikasi Pembayaran</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tinjau dan verifikasi pengajuan bukti pembayaran mahasiswa</p>
    </div>

    <!-- Alert Notifikasi Sukses -->
    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Kontainer Tabel Card -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden space-y-4 p-4 sm:p-6">
        
        <!-- Pencarian & Kontrol Filter Status -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-2">
            <form action="{{ route('keuangan.pembayaran.index') }}" method="GET" class="w-full md:w-80">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau kode..." class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-lg text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
                </div>
            </form>

            <div class="flex items-center gap-2 overflow-x-auto">
                <a href="{{ route('keuangan.pembayaran.index', ['status' => 'semua']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-lg transition {{ request('status', 'semua') == 'semua' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Semua</a>
                <a href="{{ route('keuangan.pembayaran.index', ['status' => 'pending']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-lg transition {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Menunggu</a>
                <a href="{{ route('keuangan.pembayaran.index', ['status' => 'paid']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-lg transition {{ request('status') == 'paid' ? 'bg-emerald-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Lunas</a>
                <a href="{{ route('keuangan.pembayaran.index', ['status' => 'rejected']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-lg transition {{ request('status') == 'rejected' ? 'bg-rose-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">Ditolak</a>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                <thead class="bg-slate-50/80 text-slate-400 uppercase text-[11px] font-bold border-b border-slate-100">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Penghuni</th>
                        <th class="py-3.5 px-4">Kamar</th>
                        <th class="py-3.5 px-4">Jumlah</th>
                        <th class="py-3.5 px-4">Metode</th>
                        <th class="py-3.5 px-4">Bukti</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $pembayaran)
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Kode -->
                            <td class="py-3.5 px-4 font-mono text-slate-500 font-medium">{{ $pembayaran->kode_pembayaran }}</td>
                            
                            <!-- Penghuni -->
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $pembayaran->user->nama ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400 font-mono block">{{ $pembayaran->user->nim_nik ?? '-' }}</span>
                            </td>

                            <!-- Kamar -->
                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                @if($pembayaran->reservasi && $pembayaran->reservasi->kamar)
                                    Kamar {{ $pembayaran->reservasi->kamar->nomor_kamar }}
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>

                            <!-- Jumlah -->
                            <td class="py-3.5 px-4 font-bold text-slate-900">
                                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
                            </td>

                            <!-- Metode -->
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $pembayaran->metode_pembayaran }}
                            </td>

                            <!-- Thumbnail Bukti -->
                            <td class="py-3.5 px-4">
                                @if($pembayaran->bukti_pembayaran)
                                    <button @click="showModalProof = true; proofUrl = '{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}'" class="w-10 h-10 rounded-lg overflow-hidden border border-slate-200 hover:border-blue-500 transition block">
                                        <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" alt="Bukti Transfer" class="w-full h-full object-cover">
                                    </button>
                                @else
                                    <span class="text-xs text-slate-400 italic">Tidak Ada File</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if($pembayaran->status === 'pending')
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-amber-100 text-amber-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @elseif($pembayaran->status === 'paid')
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-rose-100 text-rose-800 rounded-full inline-flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    @if($pembayaran->bukti_pembayaran)
                                        <button @click="showModalProof = true; proofUrl = '{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}'" class="p-1.5 text-slate-400 hover:text-slate-700 transition" title="Lihat Bukti">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>
                                    @endif

                                    @if($pembayaran->status === 'pending')
                                        <button @click="showModalVerify = true; selectedPayment = {{ json_encode($pembayaran) }}" class="px-3 py-1.5 bg-emerald-50 text-emerald-600 border border-emerald-200 text-xs font-semibold rounded-lg hover:bg-emerald-100 transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                            Verifikasi
                                        </button>
                                        <button @click="showModalReject = true; selectedPayment = {{ json_encode($pembayaran) }}" class="px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-200 text-xs font-semibold rounded-lg hover:bg-rose-100 transition inline-flex items-center gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                            Tolak
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">Belum ada data transaksi pembayaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL VERIFIKASI -->
    <div x-show="showModalVerify" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Verifikasi Pembayaran</h3>
            <p class="text-xs text-slate-600">Apakah Anda yakin ingin memverifikasi pembayaran sebesar <strong x-text="'Rp ' + Number(selectedPayment.jumlah_bayar).toLocaleString('id-ID')"></strong> dari <strong x-text="selectedPayment.user?.nama"></strong>?</p>
            
            <form :action="'/keuangan/pembayaran/' + selectedPayment.id + '/verify'" method="POST" class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                @csrf
                @method('PATCH')
                <button type="button" @click="showModalVerify = false" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-semibold hover:bg-emerald-700 transition">Ya, Verifikasi</button>
            </form>
        </div>
    </div>

    <!-- MODAL TOLAK -->
    <div x-show="showModalReject" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Tolak Pembayaran</h3>
            
            <form :action="'/keuangan/pembayaran/' + selectedPayment.id + '/reject'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')
                <p class="text-slate-600">Alasan penolakan bukti pembayaran untuk <strong x-text="selectedPayment.user?.nama"></strong>:</p>
                <div>
                    <textarea name="catatan_keuangan" rows="3" placeholder="Tuliskan alasan penolakan (misal: nominal tidak sesuai atau bukti transfer buram)" required class="w-full border-slate-300 rounded-lg text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]"></textarea>
                </div>
                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showModalReject = false" class="px-4 py-2 border border-slate-300 rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-lg text-xs font-semibold hover:bg-rose-700 transition">Tolak Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PRATINJAU BUKTI -->
    <div x-show="showModalProof" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/60 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-4 space-y-3 relative">
            <div class="flex justify-between items-center border-b border-slate-100 pb-2">
                <h3 class="text-sm font-bold text-slate-900">Bukti Transfer Pembayaran</h3>
                <button @click="showModalProof = false" class="text-slate-400 hover:text-slate-600 text-lg font-bold">&times;</button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto rounded-lg bg-slate-100 p-2 flex justify-center">
                <img :src="proofUrl" alt="Detail Bukti Transfer" class="max-w-full h-auto rounded-md shadow-sm">
            </div>
        </div>
    </div>

</x-layouts.keuangan>