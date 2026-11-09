<x-guest-layout>
    <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-6 shadow-xl sm:p-8">
        <h1 class="text-xl font-bold text-slate-900">Buat kata sandi baru</h1>
        <p class="mt-2 text-sm text-slate-600">Perubahan kata sandi bersifat opsional. Gunakan minimal 8 karakter.</p>
        <form method="POST" action="{{ route('password.first-login.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="current_password" class="mb-1 block text-sm font-medium text-slate-700">Kata sandi saat ini</label>
                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="w-full rounded-lg border-slate-300">
                @error('current_password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Kata sandi baru</label>
                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border-slate-300">
                @error('password')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password_confirmation" class="mb-1 block text-sm font-medium text-slate-700">Ulangi kata sandi</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password" class="w-full rounded-lg border-slate-300">
            </div>
            <button class="w-full rounded-lg bg-blue-700 px-4 py-3 font-semibold text-white hover:bg-blue-800">Simpan kata sandi</button>
        </form>
        <form method="POST" action="{{ route('logout') }}" class="mt-4 text-center">@csrf<button class="text-sm text-slate-500 underline">Keluar</button></form>
    </div>
</x-guest-layout>
