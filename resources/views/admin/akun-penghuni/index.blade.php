<x-layouts.admin title="Akun Penghuni - Asrama INSTIKI" activeMenu="akun">

    @php
        $inputClass = 'w-full rounded-lg border-slate-300 text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24]';
        $labelClass = 'mb-1 block text-xs font-semibold text-slate-700';
        $filterLabel = 'mb-1 block text-[11px] font-semibold uppercase tracking-wide text-slate-500';
    @endphp

    <!-- Heading + tombol buat akun -->
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Akun Penghuni</h1>
            <p class="mt-1 text-xs text-slate-500 sm:text-sm">Buat, perbarui identitas, aktifkan, nonaktifkan, dan reset akses akun tanpa menghapus histori.</p>
        </div>

        {{-- Pop up: Buat akun (otomatis terbuka lagi kalau validasi gagal) --}}
        <x-admin.modal title="Buat Akun Penghuni" maxWidth="40rem" :open="$errors->any() && old('nama') !== null">
            <x-slot name="trigger">
                <button type="button" class="inline-flex items-center gap-2 rounded-lg bg-[#ed1c24] px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    Buat Akun
                </button>
            </x-slot>

            <form method="POST" action="{{ route('admin.akun-penghuni.store') }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label class="{{ $labelClass }}">Nama</label>
                        <input name="nama" value="{{ old('nama') }}" required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">NIM</label>
                        <input name="nim" value="{{ old('nim') }}" minlength="8" required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">NIK (16 digit)</label>
                        <input name="nik" value="{{ old('nik') }}" inputmode="numeric" pattern="[0-9]{16}" maxlength="16" required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Jenis kelamin</label>
                        <select name="jenis_kelamin" required class="{{ $inputClass }}">
                            <option value="">Pilih</option>
                            <option value="putra" @selected(old('jenis_kelamin') === 'putra')>Laki-laki / Putra</option>
                            <option value="putri" @selected(old('jenis_kelamin') === 'putri')>Perempuan / Putri</option>
                        </select>
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Email login</label>
                        <input name="email" type="email" value="{{ old('email') }}" required class="{{ $inputClass }}">
                    </div>
                    <div>
                        <label class="{{ $labelClass }}">Nomor telepon</label>
                        <input name="no_telepon" value="{{ old('no_telepon') }}" class="{{ $inputClass }}">
                    </div>
                </div>

                <p class="rounded-lg bg-amber-50 px-3 py-2 text-[11px] leading-relaxed text-amber-800">Username dan password awal menggunakan NIM (minimal 8 karakter). Penghuni dapat mengganti password melalui Profil kapan saja.</p>

                <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                    <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                    <button class="rounded-lg bg-[#ed1c24] px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Buat Akun</button>
                </div>
            </form>
        </x-admin.modal>
    </div>

    <!-- Notifikasi -->
    @if(session('success'))
        <div class="rounded-lg border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">{{ session('success') }}</div>
    @endif

    @if(session('login_password'))
        <div x-data="{ copied: false }" class="flex flex-wrap items-center gap-3 rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span>Username (NIM): <code class="rounded bg-white px-2 py-1 font-mono font-bold">{{ session('login_username') }}</code></span>
            <span>Password (sama dengan NIM):</span>
            <code class="rounded bg-white px-2 py-1 font-mono font-bold">{{ session('login_password') }}</code>
            <button type="button"
                    data-copy-value="{{ session('login_password') }}"
                    @click="navigator.clipboard.writeText($el.dataset.copyValue); copied = true; setTimeout(() => copied = false, 1500)"
                    class="rounded border border-amber-300 bg-white px-2.5 py-1 text-xs font-semibold text-amber-800 transition hover:bg-amber-100">
                <span x-text="copied ? 'Tersalin' : 'Salin'"></span>
            </button>
        </div>
    @endif

    @foreach($errors->all() as $error)
        <div class="rounded-lg border border-rose-200 bg-rose-50 p-3 text-sm text-rose-800">{{ $error }}</div>
    @endforeach

    <!-- Filter -->
    <form method="GET" action="{{ route('admin.akun-penghuni.index') }}" class="grid grid-cols-1 items-end gap-3 rounded-lg border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-2 lg:grid-cols-6">
        <div class="lg:col-span-2">
            <label class="{{ $filterLabel }}">Pencarian</label>
            <input name="search" value="{{ request('search') }}" placeholder="Nama, NIM, NIK, email" class="{{ $inputClass }}">
        </div>

        <div>
            <label class="{{ $filterLabel }}">Jenis kelamin</label>
            <select name="jenis_kelamin" class="{{ $inputClass }}">
                <option value="">Semua</option>
                <option value="putra" @selected(request('jenis_kelamin') === 'putra')>Putra</option>
                <option value="putri" @selected(request('jenis_kelamin') === 'putri')>Putri</option>
            </select>
        </div>

        <div>
            <label class="{{ $filterLabel }}">Status</label>
            <select name="aktif" class="{{ $inputClass }}">
                <option value="">Semua status</option>
                <option value="1" @selected(request('aktif') === '1')>Aktif</option>
                <option value="0" @selected(request('aktif') === '0')>Nonaktif</option>
            </select>
        </div>

        <button class="rounded-lg bg-slate-800 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-900">Filter</button>
        <a href="{{ route('admin.akun-penghuni.index') }}" class="rounded-lg border border-slate-300 px-4 py-2 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">Reset</a>
    </form>

    <!-- Tabel -->
    <div class="overflow-hidden rounded-lg border border-slate-200 bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full divide-y divide-slate-200 text-left text-sm" style="min-width: 820px;">
                <thead class="bg-slate-50 text-[11px] font-bold uppercase tracking-wide text-slate-600">
                    <tr>
                        <th class="px-4 py-3.5">Penghuni</th>
                        <th class="px-4 py-3.5">NIM / NIK / Jenis Kelamin</th>
                        <th class="px-4 py-3.5">Status Akses</th>
                        <th class="px-4 py-3.5">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="align-middle transition hover:bg-slate-50/60">

                            {{-- Penghuni --}}
                            <td class="px-4 py-4">
                                <strong class="block text-slate-900">{{ $user->nama }}</strong>
                                <span class="block text-xs text-slate-500">{{ $user->email }}</span>
                            </td>

                            {{-- NIM / NIK --}}
                            <td class="px-4 py-4">
                                <span class="block text-slate-700">NIM: {{ $user->nim_nik }}</span>
                                <span class="block text-xs text-slate-500">NIK: {{ $user->nik ?? 'Belum diisi' }} · {{ $user->jenis_kelamin ? ucfirst($user->jenis_kelamin) : 'Belum ditetapkan' }}</span>
                            </td>

                            {{-- Status --}}
                            <td class="px-4 py-4">
                                <span class="inline-block rounded-full px-2.5 py-1 text-[11px] font-bold {{ $user->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                                <span class="mt-1 block text-xs text-slate-500">Ganti password melalui Profil (opsional)</span>
                            </td>

                            {{-- Aksi --}}
                            <td class="px-4 py-4">
                                <div class="flex flex-wrap items-center gap-2">

                                    {{-- Pop up: Perbarui identitas --}}
                                    <x-admin.modal title="Perbarui Identitas" maxWidth="40rem">
                                        <x-slot name="trigger">
                                            <button type="button" class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">Edit</button>
                                        </x-slot>

                                        <form method="POST" action="{{ route('admin.akun-penghuni.update', $user) }}" class="space-y-4">
                                            @csrf
                                            @method('PUT')

                                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                <div>
                                                    <label class="{{ $labelClass }}">Nama</label>
                                                    <input name="nama" value="{{ $user->nama }}" required class="{{ $inputClass }}">
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">NIM</label>
                                                    <input name="nim" value="{{ $user->nim_nik }}" minlength="8" required class="{{ $inputClass }}">
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">NIK (16 digit)</label>
                                                    <input name="nik" value="{{ $user->nik }}" required pattern="[0-9]{16}" maxlength="16" class="{{ $inputClass }}">
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">Jenis kelamin</label>
                                                    <select name="jenis_kelamin" required class="{{ $inputClass }}">
                                                        <option value="putra" @selected($user->jenis_kelamin === 'putra')>Putra</option>
                                                        <option value="putri" @selected($user->jenis_kelamin === 'putri')>Putri</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">Email</label>
                                                    <input name="email" type="email" value="{{ $user->email }}" required class="{{ $inputClass }}">
                                                </div>
                                                <div>
                                                    <label class="{{ $labelClass }}">Nomor telepon</label>
                                                    <input name="no_telepon" value="{{ $user->no_telepon }}" class="{{ $inputClass }}">
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                <button class="rounded-lg bg-[#ed1c24] px-4 py-2 text-xs font-semibold text-white transition hover:bg-red-700">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </x-admin.modal>

                                    {{-- Pop up: Aktifkan / nonaktifkan --}}
                                    <x-admin.modal :title="$user->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun'">
                                        <x-slot name="trigger">
                                            <button type="button" class="rounded-lg border px-3 py-1.5 text-xs font-semibold transition {{ $user->is_active ? 'border-rose-300 text-rose-700 hover:bg-rose-50' : 'border-emerald-300 text-emerald-700 hover:bg-emerald-50' }}">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                        </x-slot>

                                        <form method="POST" action="{{ route('admin.akun-penghuni.toggle', $user) }}" class="space-y-4">
                                            @csrf
                                            @method('PATCH')

                                            <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                <span class="block text-sm font-semibold text-slate-900">{{ $user->nama }}</span>
                                                <span class="block">{{ $user->email }}</span>
                                            </div>

                                            <p class="text-sm text-slate-600">
                                                {{ $user->is_active ? 'Akun ini tidak akan bisa login setelah dinonaktifkan. Histori tetap tersimpan.' : 'Akun ini akan bisa login kembali setelah diaktifkan.' }}
                                            </p>

                                            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                <button class="rounded-lg px-4 py-2 text-xs font-semibold text-white transition {{ $user->is_active ? 'bg-rose-700 hover:bg-rose-800' : 'bg-emerald-700 hover:bg-emerald-800' }}">{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                                            </div>
                                        </form>
                                    </x-admin.modal>

                                    {{-- Pop up: Reset password --}}
                                    <x-admin.modal title="Reset Password">
                                        <x-slot name="trigger">
                                            <button type="button" class="rounded-lg border border-amber-300 px-3 py-1.5 text-xs font-semibold text-amber-800 transition hover:bg-amber-50">Reset password</button>
                                        </x-slot>

                                        <form method="POST" action="{{ route('admin.akun-penghuni.reset-password', $user) }}" class="space-y-4">
                                            @csrf
                                            @method('PATCH')

                                            <div class="rounded-lg bg-slate-50 px-3 py-2.5 text-xs text-slate-600">
                                                <span class="block text-sm font-semibold text-slate-900">{{ $user->nama }}</span>
                                                <span class="block">{{ $user->email }}</span>
                                            </div>

                                            <p class="text-sm text-slate-600">Password akun ini akan direset menjadi NIM penghuni. Setelah itu penghuni dapat login memakai NIM sebagai username dan password, lalu mengganti password melalui Profil jika diinginkan.</p>

                                            <div class="flex justify-end gap-2 border-t border-slate-100 pt-4">
                                                <button type="button" @click="open = false" class="rounded-lg border border-slate-300 px-4 py-2 text-xs font-medium text-slate-700 transition hover:bg-slate-50">Batal</button>
                                                <button class="rounded-lg bg-amber-600 px-4 py-2 text-xs font-semibold text-white transition hover:bg-amber-700">Reset Password</button>
                                            </div>
                                        </form>
                                    </x-admin.modal>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-500">Belum ada akun penghuni.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-slate-100 p-4">{{ $users->links() }}</div>
    </div>
</x-layouts.admin>
