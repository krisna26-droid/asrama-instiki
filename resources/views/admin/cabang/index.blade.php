<x-layouts.admin title="Cabang Asrama - Admin Asrama INSTIKI" activeMenu="cabang">
    <header>
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-[#ed1c24]">Lokasi operasional</p>
        <h1 class="mt-1 text-2xl font-bold text-slate-900">Cabang Asrama</h1>
        <p class="mt-1 text-sm text-slate-500">Kelola titik lokasi dan alamat cabang. Penetapan cabang penghuni dilakukan Admin Asrama saat menyetujui reservasi.</p>
    </header>

    @if(session('success'))<div role="status" class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>@endif
    @foreach($errors->all() as $error)<div role="alert" class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>@endforeach

    <section class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <h2 class="font-bold text-slate-900">Tambah cabang</h2>
        <form method="POST" action="{{ route('admin.cabang.store') }}" class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-2">
            @csrf
            <label class="text-xs font-semibold text-slate-700">Kode<input name="kode" required maxlength="30" value="{{ old('kode') }}" placeholder="Contoh: DENPASAR-01" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-700">Nama cabang<input name="nama" required maxlength="150" value="{{ old('nama') }}" placeholder="Nama lokasi asrama" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-700 md:col-span-2">Alamat<textarea name="alamat" required rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm">{{ old('alamat') }}</textarea></label>
            <label class="text-xs font-semibold text-slate-700">Tautan peta<input type="url" name="maps_url" value="{{ old('maps_url') }}" placeholder="https://maps.google.com/..." class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-700">Telepon<input name="telepon" maxlength="50" value="{{ old('telepon') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
            <label class="text-xs font-semibold text-slate-700">WhatsApp<input name="whatsapp" maxlength="50" value="{{ old('whatsapp') }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
            <div class="flex items-end"><button class="rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white hover:bg-slate-700">Simpan cabang</button></div>
        </form>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
        @forelse($cabangs as $cabang)
            <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div><p class="font-mono text-xs text-slate-400">{{ $cabang->kode }}</p><h2 class="text-lg font-bold text-slate-900">{{ $cabang->nama }}</h2><p class="mt-1 text-sm text-slate-600">{{ $cabang->alamat }}</p></div>
                    <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $cabang->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ $cabang->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                </div>
                @if($cabang->maps_url)<a href="{{ $cabang->maps_url }}" target="_blank" rel="noopener noreferrer" class="mt-3 inline-block text-sm font-semibold text-blue-700">Lihat peta ↗</a>@endif
                <div class="mt-4 rounded-lg bg-slate-50 p-3 text-xs text-slate-600">{{ $cabang->kamars->count() }} kamar · kapasitas {{ $cabang->kamars->sum('kapasitas') }} orang @if($cabang->telepon) · {{ $cabang->telepon }} @endif</div>
                <details class="mt-4 rounded-lg border border-slate-200 p-3"><summary class="cursor-pointer text-sm font-semibold text-slate-700">Edit cabang</summary>
                    <form method="POST" action="{{ route('admin.cabang.update', $cabang) }}" class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @csrf @method('PUT')
                        <label class="text-xs font-semibold text-slate-700">Kode<input name="kode" required maxlength="30" value="{{ $cabang->kode }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
                        <label class="text-xs font-semibold text-slate-700">Nama<input name="nama" required maxlength="150" value="{{ $cabang->nama }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
                        <label class="text-xs font-semibold text-slate-700 sm:col-span-2">Alamat<textarea name="alamat" required rows="2" class="mt-1 w-full rounded-lg border-slate-300 text-sm">{{ $cabang->alamat }}</textarea></label>
                        <label class="text-xs font-semibold text-slate-700">Tautan peta<input type="url" name="maps_url" value="{{ $cabang->maps_url }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
                        <label class="text-xs font-semibold text-slate-700">Telepon<input name="telepon" maxlength="50" value="{{ $cabang->telepon }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
                        <label class="text-xs font-semibold text-slate-700">WhatsApp<input name="whatsapp" maxlength="50" value="{{ $cabang->whatsapp }}" class="mt-1 w-full rounded-lg border-slate-300 text-sm"></label>
                        <div class="flex items-end"><button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white">Simpan perubahan</button></div>
                    </form>
                </details>
                <form method="POST" action="{{ route('admin.cabang.toggle', $cabang) }}" class="mt-3" onsubmit="return confirm('{{ $cabang->is_active ? 'Nonaktifkan' : 'Aktifkan' }} cabang ini? Placement dan histori tidak akan dihapus.')">
                    @csrf @method('PATCH')<button class="rounded-lg border px-4 py-2 text-sm font-semibold {{ $cabang->is_active ? 'border-amber-300 text-amber-800' : 'border-emerald-300 text-emerald-800' }}">{{ $cabang->is_active ? 'Nonaktifkan cabang' : 'Aktifkan cabang' }}</button>
                </form>
            </article>
        @empty
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-center text-sm text-slate-500">Belum ada cabang. Tambahkan cabang terlebih dahulu sebelum menetapkan kamar.</div>
        @endforelse
    </section>
    <div>{{ $cabangs->links() }}</div>
</x-layouts.admin>
