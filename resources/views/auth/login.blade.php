<x-guest-layout>

<div class="w-full max-w-lg bg-white border border-slate-200 rounded-2xl shadow-xl p-8">

    <!-- Logo -->
    <div class="flex flex-col items-center mb-8">
        <a href="{{ url('/') }}" class="flex items-center space-x-3">
            <img
                src="{{ asset('image/instiki-logo.png') }}"
                alt="Logo INSTIKI"
                class="h-12 w-auto object-contain"
            >

            <div class="border-l border-slate-300 pl-3 text-left">
                <span class="block text-xs font-bold uppercase tracking-wider text-slate-800">
                    Asrama Kampus
                </span>

                <span class="text-[10px] text-slate-500">
                    Institut Bisnis dan Teknologi Indonesia
                </span>
            </div>
        </a>
    </div>

    <!-- Header -->
    <div class="text-center mb-8">

        <span
            class="inline-flex items-center rounded-md border border-[#ed1c24]/20 bg-[#ed1c24]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-[#ed1c24]">
            Official Housing Portal INSTIKI
        </span>

        <h1 class="mt-5 text-3xl font-bold text-slate-900">
            Masuk ke Sistem
        </h1>

        <p class="mt-2 text-sm text-slate-600">
            Silakan masukkan email dan password Anda.
        </p>

    </div>

    <x-auth-session-status
        class="mb-5"
        :status="session('status')"
    />

    <form method="POST" action="{{ route('login') }}">

        @csrf

        <!-- Email -->
        <div>

            <x-input-label
                for="email"
                :value="__('Email')"
                class="text-sm font-semibold text-slate-700"
            />

            <x-text-input
                id="email"
                class="mt-2 block w-full rounded-lg border-slate-300 focus:border-[#ed1c24] focus:ring-[#ed1c24]"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan alamat email"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')"
            />

        </div>

        <!-- Password dengan Fitur Toggle Lihat Password -->
        <div class="mt-5">

            <x-input-label
                for="password"
                :value="__('Password')"
                class="text-sm font-semibold text-slate-700"
            />

            <div class="relative mt-2">
                <x-text-input
                    id="password"
                    class="block w-full rounded-lg border-slate-300 pr-10 focus:border-[#ed1c24] focus:ring-[#ed1c24]"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                />

                <button 
                    type="button" 
                    onclick="togglePassword()" 
                    class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600 focus:outline-none"
                    tabindex="-1"
                >
                    <!-- Ikon Mata Terbuka -->
                    <svg id="eye-open" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>

                    <!-- Ikon Mata Tertutup (Hidden) -->
                    <svg id="eye-closed" class="h-5 w-5 hidden" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.04 10.04 0 013.682-.763c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M3 3l18 18" />
                    </svg>
                </button>
            </div>

            <x-input-error
                class="mt-2"
                :messages="$errors->get('password')"
            />

        </div>

        <!-- Remember -->
        <div class="mt-6 flex items-center justify-between">

            <label
                for="remember_me"
                class="inline-flex items-center"
            >
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="rounded border-slate-300 text-[#ed1c24] shadow-sm focus:ring-[#ed1c24]"
                >

                <span class="ml-2 text-sm text-slate-600">
                    Remember me
                </span>
            </label>

        </div>

        <!-- Action -->
        <div class="mt-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            @if (Route::has('password.request'))

                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-slate-600 hover:text-[#ed1c24] transition"
                >
                    Forgot your password?
                </a>

            @endif

            <x-primary-button
                class="justify-center rounded-lg bg-[#ed1c24] px-6 py-3 hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:ring-[#ed1c24]"
            >
                Log In
            </x-primary-button>

        </div>

    </form>

    @if (Route::has('register'))

        <div class="mt-8 border-t border-slate-200 pt-6 text-center">

            <p class="text-sm text-slate-600">

                Belum memiliki akun?

                <a
                    href="{{ route('register') }}"
                    class="font-semibold text-[#ed1c24] hover:text-red-700"
                >
                    Daftar Akun
                </a>

            </p>

        </div>

    @endif

</div>

<!-- Script Toggle Password -->
<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeOpen = document.getElementById('eye-open');
        const eyeClosed = document.getElementById('eye-closed');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            passwordInput.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>

</x-guest-layout>