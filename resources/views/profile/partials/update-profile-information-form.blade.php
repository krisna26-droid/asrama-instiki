<section class="space-y-4">
    <header>
        <h2 class="text-base font-bold text-slate-900">Informasi Profil</h2>
        <p class="mt-1 text-xs text-slate-500">Data identitas dikelola Admin Asrama dan tidak dapat diubah dari halaman ini.</p>
    </header>

    <dl class="grid grid-cols-1 gap-3 rounded-xl border border-slate-200 bg-white p-4 text-sm sm:grid-cols-2">
        <div>
            <dt class="text-xs text-slate-500">Nama</dt>
            <dd class="mt-1 font-medium text-slate-900">{{ $user->nama ?? $user->name }}</dd>
        </div>
        <div>
            <dt class="text-xs text-slate-500">Email</dt>
            <dd class="mt-1 font-medium text-slate-900">{{ $user->email }}</dd>
        </div>
        @if($user->nik)
            <div>
                <dt class="text-xs text-slate-500">NIK</dt>
                <dd class="mt-1 font-mono text-slate-900">{{ $user->nik }}</dd>
            </div>
        @endif
    </dl>
</section>
