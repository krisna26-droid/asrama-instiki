<x-layouts.admin title="Detail Penempatan" activeMenu="penghuni">
    <header class="flex flex-wrap items-center justify-between gap-3"><div><a href="{{ route('admin.penghuni.index') }}" class="text-sm text-blue-700 underline">Kembali ke penempatan</a><h1 class="mt-2 text-2xl font-bold">Detail penghuni dan penempatan</h1></div><span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold">{{ ucfirst($placement->status) }}</span></header>
    @if(session('success'))<div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @foreach($errors->all() as $error)<div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>@endforeach
    <div class="grid grid-cols-1 gap-5 lg:grid-cols-3">
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm"><h2 class="font-semibold">Identitas penghuni</h2><dl class="mt-4 space-y-3 text-sm"><div><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ $placement->user->nama }}</dd></div><div><dt class="text-slate-500">NIK</dt><dd class="font-mono">{{ $placement->user->nik ?? 'Belum diisi' }}</dd></div><div><dt class="text-slate-500">Jenis kelamin</dt><dd>{{ ucfirst($placement->user->jenis_kelamin ?? '—') }}</dd></div><div><dt class="text-slate-500">Email</dt><dd>{{ $placement->user->email }}</dd></div><div><dt class="text-slate-500">Telepon</dt><dd>{{ $placement->user->no_telepon ?? '—' }}</dd></div></dl>
            @if($placement->reservasi?->foto_ktp_path)<a target="_blank" rel="noopener" class="mt-5 inline-flex rounded-lg bg-blue-700 px-4 py-2 text-sm font-semibold text-white" href="{{ route('dokumen.ktp', $placement->reservasi) }}">Lihat dokumen KTP</a>@endif
        </section>
        <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm lg:col-span-2"><h2 class="font-semibold">Riwayat placement</h2><dl class="mt-4 grid grid-cols-1 gap-4 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Kode reservasi</dt><dd>{{ $placement->reservasi->kode_reservasi ?? '—' }}</dd></div><div><dt class="text-slate-500">Kamar</dt><dd>{{ $placement->kamar->nomor_kamar ?? '—' }} @if($placement->kamar) · Blok {{ $placement->kamar->blok }} @endif</dd></div><div><dt class="text-slate-500">Periode (akhir eksklusif)</dt><dd>{{ $placement->tanggal_mulai->format('d M Y') }} – {{ $placement->tanggal_akhir->format('d M Y') }}</dd></div><div><dt class="text-slate-500">Check-in</dt><dd>{{ $placement->check_in_at?->format('d M Y H:i') ?? 'Belum dicatat' }} @if($placement->check_in_oleh)· {{ $placement->checkInOleh->nama ?? '' }}@endif</dd></div><div><dt class="text-slate-500">Check-out aktual</dt><dd>{{ $placement->check_out_at?->format('d M Y H:i') ?? 'Belum dicatat' }} @if($placement->check_out_oleh)· {{ $placement->checkOutOleh->nama ?? '' }}@endif</dd></div><div><dt class="text-slate-500">Transfer asal</dt><dd>{{ $placement->transferDari?->kamar?->nomor_kamar ?? '—' }}</dd></div></dl>
            @if($placement->catatan_lifecycle)<div class="mt-5 rounded-lg bg-slate-50 p-4 text-sm whitespace-pre-line">{{ $placement->catatan_lifecycle }}</div>@endif
        </section>
        @if($placement->status === 'scheduled' && !$placement->check_in_at && !$placement->transfer_dari_id && $placement->reservasi?->status === 'approved' && $placement->tanggal_mulai->toDateString() >= now()->toDateString())
            <section class="rounded-xl border border-amber-200 bg-amber-50 p-5 shadow-sm lg:col-span-3">
                <h2 class="font-semibold text-amber-950">Ubah jadwal check-in</h2>
                <p class="mt-1 text-sm text-amber-900">Perubahan menggeser tanggal akhir sesuai durasi yang disetujui, memeriksa ulang kapasitas kamar, dan tidak mengubah nominal tagihan. Waktu check-in aktual baru tercatat saat admin menekan tombol Check-in.</p>
                <form method="POST" action="{{ route('admin.penempatan.check-in.schedule.update', $placement) }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3" onsubmit="return confirm('Simpan jadwal check-in baru dan perbarui periode penempatan?');">
                    @csrf @method('PATCH')
                    <input type="hidden" name="tanggal_mulai_saat_form" value="{{ $placement->tanggal_mulai->format('Y-m-d') }}">
                    <label class="text-xs font-semibold text-slate-700">Tanggal check-in yang direncanakan
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai', $placement->tanggal_mulai->format('Y-m-d')) }}" min="{{ now()->toDateString() }}" required class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <label class="text-xs font-semibold text-slate-700 sm:col-span-2">Alasan perubahan
                        <input name="alasan" value="{{ old('alasan') }}" required maxlength="2000" placeholder="Contoh: jadwal kedatangan mahasiswa berubah" class="mt-1 w-full rounded-lg border-slate-300 text-sm">
                    </label>
                    <div class="sm:col-span-3"><button class="rounded-lg bg-amber-700 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-800">Simpan jadwal baru</button></div>
                </form>
            </section>
        @elseif($placement->status === 'scheduled' && $placement->tanggal_mulai->toDateString() < now()->toDateString())
            <section class="rounded-xl border border-rose-200 bg-rose-50 p-5 shadow-sm lg:col-span-3">
                <h2 class="font-semibold text-rose-950">Jadwal check-in terlewat</h2>
                <p class="mt-1 text-sm text-rose-900">Penjadwalan ulang ditutup untuk jadwal yang sudah lewat agar catatan ketidakhadiran tidak tertimpa. Placement ini memerlukan tindak lanjut administratif.</p>
            </section>
        @endif
        @if($placement->status === 'active')
            <section class="rounded-xl border border-blue-100 bg-blue-50 p-5 shadow-sm lg:col-span-3">
                <h2 class="font-semibold text-blue-950">Transfer langsung ke kamar lain</h2>
                <p class="mt-1 text-sm text-blue-900">Transfer dicatat sekarang: sistem menutup placement asal dan mengaktifkan placement tujuan secara atomik.</p>
                <form method="POST" action="{{ route('admin.penempatan.transfer', $placement) }}" class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-3" onsubmit="return confirm('Catat checkout kamar asal dan check-in kamar tujuan sekarang?');">
                    @csrf
                    <select name="kamar_id" required class="rounded-lg border-slate-300"><option value="">Pilih kamar tujuan</option>@foreach($kamarsTujuan as $kamarTujuan)<option value="{{ $kamarTujuan->id }}">Kamar {{ $kamarTujuan->nomor_kamar }} · Blok {{ $kamarTujuan->blok }} (kapasitas {{ $kamarTujuan->kapasitas }})</option>@endforeach</select>
                    <input name="alasan" required maxlength="2000" placeholder="Alasan transfer" class="rounded-lg border-slate-300">
                    <button class="rounded-lg bg-blue-800 px-4 py-2 text-sm font-semibold text-white">Catat transfer</button>
                </form>
            </section>
        @endif
    </div>
</x-layouts.admin>
