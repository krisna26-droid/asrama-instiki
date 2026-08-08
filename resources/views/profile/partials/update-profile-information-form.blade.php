<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-slate-900">
            Informasi Profil
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Perbarui informasi profil akun dan alamat email Anda.
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <!-- Nama Lengkap -->
        <div>
            <label for="nama" class="block text-xs font-semibold text-slate-700 mb-1">Nama Lengkap</label>
            <input id="nama" name="nama" type="text" value="{{ old('nama', $user->nama ?? $user->name) }}" required autofocus autocomplete="name" 
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
            <x-input-error class="mt-1 text-xs text-rose-600" :messages="$errors->get('nama')" />
        </div>

        <!-- Email -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 mb-1">Alamat Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username" 
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
            <x-input-error class="mt-1 text-xs text-rose-600" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-2">
                    <p class="text-xs text-amber-800 bg-amber-50 p-3 rounded-lg border border-amber-200">
                        Alamat email Anda belum terverifikasi.
                        <button form="send-verification" class="underline font-bold text-amber-900 hover:text-amber-700 ml-1">
                            Klik di sini untuk mengirim ulang email verifikasi.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-bold text-xs text-emerald-600">
                            Tautan verifikasi baru telah dikirimkan ke alamat email Anda.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-[#ed1c24] hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                Simpan Perubahan
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>