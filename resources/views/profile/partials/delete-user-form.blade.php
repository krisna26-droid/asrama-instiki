<section class="space-y-6">
    <header>
        <h2 class="text-base font-bold text-rose-600">
            Hapus Akun
        </h2>
        <p class="mt-1 text-xs text-slate-500">
            Setelah akun Anda dihapus, semua sumber daya dan datanya akan dihapus secara permanen.
        </p>
    </header>

    <button type="button" 
            x-data="" 
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-5 py-2.5 bg-rose-50 text-rose-600 border border-rose-200 font-bold text-xs rounded-xl hover:bg-rose-100 transition">
        Hapus Akun
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6 space-y-4">
            @csrf
            @method('delete')

            <h2 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">
                Apakah Anda yakin ingin menghapus akun?
            </h2>

            <p class="text-xs text-slate-600 leading-relaxed">
                Setelah akun Anda dihapus, seluruh data di portal asrama akan dihapus secara permanen. Masukkan kata sandi Anda untuk mengonfirmasi bahwa Anda ingin menghapus akun secara permanen.
            </p>

            <div>
                <label for="password" class="sr-only">Kata Sandi</label>
                <input id="password" name="password" type="password" placeholder="Masukkan Kata Sandi Anda" 
                       class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs sm:text-sm focus:border-rose-600 focus:ring-rose-600 transition">
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1 text-xs text-rose-600" />
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-slate-100">
                <button type="button" x-on:click="$dispatch('close')" class="px-4 py-2 border border-slate-300 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-rose-600 text-white rounded-xl text-xs font-semibold hover:bg-rose-700 transition">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </x-modal>
</section>