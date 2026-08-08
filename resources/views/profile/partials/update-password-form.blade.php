<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-slate-900">
            Perbarui Kata Sandi
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Pastikan akun Anda menggunakan kata sandi yang panjang dan acak agar tetap aman.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('put')

        <!-- Kata Sandi Saat Ini -->
        <div>
            <label for="update_password_current_password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Saat Ini</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password" 
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Kata Sandi Baru -->
        <div>
            <label for="update_password_password" class="block text-xs font-semibold text-slate-700 mb-1">Kata Sandi Baru</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password" 
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Konfirmasi Kata Sandi -->
        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-semibold text-slate-700 mb-1">Konfirmasi Kata Sandi Baru</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" 
                   class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-[#ed1c24] focus:ring-[#ed1c24] focus:bg-white transition">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1 text-xs text-rose-600" />
        </div>

        <!-- Tombol Simpan -->
        <div class="flex items-center gap-4 pt-2">
            <button type="submit" class="px-5 py-2.5 bg-[#ed1c24] hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow-sm transition">
                Simpan Kata Sandi
            </button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)"
                   class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>