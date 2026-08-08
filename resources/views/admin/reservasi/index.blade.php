<x-layouts.admin 
    title="Verifikasi Reservasi - Admin Asrama INSTIKI" 
    activeMenu="reservasi"
    searchRoute="{{ route('admin.reservasi.index') }}"
    searchPlaceholder="Cari nama, NIM, atau kode reservasi..."
    x-data="{ showApproveModal: false, showRejectModal: false, selectedReservasi: {} }">

    <div>
        <h1 class="text-2xl font-bold text-slate-900">Verifikasi Reservasi</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Tinjau dan proses persetujuan reservasi kamar mahasiswa baru.</p>
    </div>

    @if(session('success'))
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-lg text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <!-- Filter Status Pill -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
        <a href="{{ route('admin.reservasi.index', ['status' => 'semua']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status', 'semua') == 'semua' ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Semua</a>
        <a href="{{ route('admin.reservasi.index', ['status' => 'pending']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status') == 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Menunggu</a>
        <a href="{{ route('admin.reservasi.index', ['status' => 'approved']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status') == 'approved' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Disetujui</a>
        <a href="{{ route('admin.reservasi.index', ['status' => 'rejected']) }}" class="px-4 py-1.5 text-xs font-semibold rounded-full {{ request('status') == 'rejected' ? 'bg-red-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">Ditolak</a>
    </div>

    <!-- Table Content -->
    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs sm:text-sm text-slate-600">
                <thead class="bg-slate-50 text-slate-700 uppercase text-[11px] font-bold border-b border-slate-200">
                    <tr>
                        <th class="py-3.5 px-4">Kode</th>
                        <th class="py-3.5 px-4">Penghuni</th>
                        <th class="py-3.5 px-4">Kamar</th>
                        <th class="py-3.5 px-4">Pengajuan</th>
                        <th class="py-3.5 px-4">Durasi</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($reservasis as $res)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="py-3.5 px-4 font-mono font-bold text-slate-800">{{ $res->kode_reservasi }}</td>
                            <td class="py-3.5 px-4">
                                <span class="font-bold text-slate-900 block">{{ $res->user->nama ?? '-' }}</span>
                                <span class="text-[11px] text-slate-400 block">{{ $res->user->nim_nik ?? '-' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if($res->kamar)
                                    <span class="font-bold text-slate-800">Kamar {{ $res->kamar->nomor_kamar }}</span>
                                    <span class="text-[11px] text-slate-400 block">Blok {{ $res->kamar->blok }}</span>
                                @else
                                    <span class="text-amber-600 italic font-medium">Belum Ditentukan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">{{ \Carbon\Carbon::parse($res->tanggal_pengajuan)->format('d M Y') }}</td>
                            <td class="py-3.5 px-4 font-medium">{{ $res->durasi_sewa }}</td>
                            <td class="py-3.5 px-4">
                                @if($res->status === 'pending')
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-amber-100 text-amber-800 rounded-full">Menunggu</span>
                                @elseif($res->status === 'approved')
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-emerald-100 text-emerald-800 rounded-full">Disetujui</span>
                                @elseif($res->status === 'rejected')
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-red-100 text-red-800 rounded-full">Ditolak</span>
                                @else
                                    <span class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 text-slate-600 rounded-full">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @if($res->status === 'pending')
                                    <div class="flex items-center justify-center space-x-2">
                                        <button @click="showApproveModal = true; selectedReservasi = {{ json_encode($res) }}" class="px-3 py-1.5 bg-emerald-600 text-white text-xs font-semibold rounded hover:bg-emerald-700 transition">
                                            Setujui
                                        </button>
                                        <button @click="showRejectModal = true; selectedReservasi = {{ json_encode($res) }}" class="px-3 py-1.5 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition">
                                            Tolak
                                        </button>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Proses Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">Belum ada data pengajuan reservasi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL APPROVE -->
    <div x-show="showApproveModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Setujui Reservasi</h3>
            
            <form :action="'/admin/reservasi/' + selectedReservasi.id + '/approve'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')

                <p class="text-slate-600">Konfirmasi persetujuan reservasi untuk <strong x-text="selectedReservasi.user?.nama"></strong>.</p>

                <div>
                    <label class="font-bold text-slate-700 block mb-1">Pilih / Ubah Kamar Penempatan</label>
                    <select name="kamar_id" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]">
                        <option value="">-- Pilih Kamar Tersedia --</option>
                        @foreach($kamars as $kmr)
                            <option value="{{ $kmr->id }}">Kamar {{ $kmr->nomor_kamar }} (Blok {{ $kmr->blok }} - {{ ucfirst($kmr->kategori) }} - Terisi: {{ $kmr->terisi }}/{{ $kmr->kapasitas }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showApproveModal = false" class="px-4 py-2 border border-slate-300 rounded font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-semibold rounded hover:bg-emerald-700">Setujui</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL REJECT -->
    <div x-show="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-slate-900/50 p-4" x-cloak>
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6 space-y-4">
            <h3 class="text-lg font-bold text-slate-900 border-b border-slate-100 pb-2">Tolak Reservasi</h3>
            
            <form :action="'/admin/reservasi/' + selectedReservasi.id + '/reject'" method="POST" class="space-y-4 text-xs">
                @csrf
                @method('PATCH')

                <p class="text-slate-600">Alasan penolakan pengajuan untuk <strong x-text="selectedReservasi.user?.nama"></strong>:</p>

                <div>
                    <textarea name="catatan_admin" rows="3" placeholder="Tuliskan alasan penolakan (misal: kuota kamar putra/putri sudah penuh)" required class="w-full border-slate-300 rounded text-xs focus:border-[#ed1c24] focus:ring-[#ed1c24]"></textarea>
                </div>

                <div class="flex justify-end space-x-2 pt-3 border-t border-slate-100">
                    <button type="button" @click="showRejectModal = false" class="px-4 py-2 border border-slate-300 rounded font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded hover:bg-red-700">Tolak Pengajuan</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.admin>