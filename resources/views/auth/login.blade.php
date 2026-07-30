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

        <!-- Password -->
        <div class="mt-5">

            <x-input-label
                for="password"
                :value="__('Password')"
                class="text-sm font-semibold text-slate-700"
            />

            <x-text-input
                id="password"
                class="mt-2 block w-full rounded-lg border-slate-300 focus:border-[#ed1c24] focus:ring-[#ed1c24]"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            />

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

</x-guest-layout>