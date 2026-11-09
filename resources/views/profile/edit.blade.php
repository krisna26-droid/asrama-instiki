<x-layouts.penghuni 
    title="Profil Saya - Asrama INSTIKI" 
    activeMenu="profil">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Data identitas dikelola Admin Asrama. Anda dapat memperbarui kata sandi sendiri.</p>
    </div>

    <div class="space-y-6 max-w-4xl">
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
            <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2"><div><dt class="text-slate-500">Nama</dt><dd class="font-medium">{{ auth()->user()->nama }}</dd></div><div><dt class="text-slate-500">NIK</dt><dd class="font-mono">{{ auth()->user()->nik ?? 'Belum diisi' }}</dd></div><div><dt class="text-slate-500">Email</dt><dd class="font-medium">{{ auth()->user()->email }}</dd></div></dl>
        </div>

        <!-- Card: Perbarui Kata Sandi -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

    </div>

</x-layouts.penghuni>
