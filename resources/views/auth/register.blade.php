<x-guest-layout>

<div class="w-full max-w-xl bg-white border border-slate-200 rounded-2xl shadow-xl p-8">

    <!-- Logo -->
    <div class="flex flex-col items-center mb-8">
        <a href="{{ url('/') }}" class="flex items-center space-x-3">
            <img
                src="{{ asset('image/instiki-logo.png') }}"
                alt="Logo INSTIKI"
                class="h-12 w-auto object-contain">

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

        <span class="inline-flex rounded-md border border-[#ed1c24]/20 bg-[#ed1c24]/10 px-3 py-1 text-[10px] font-bold uppercase tracking-[0.2em] text-[#ed1c24]">
            Official Housing Portal INSTIKI
        </span>

        <h1 class="mt-5 text-3xl font-bold text-slate-900">
            Daftar Akun
        </h1>

        <p class="mt-2 text-sm text-slate-600">
            Lengkapi data berikut untuk membuat akun Sistem Informasi Pengelolaan & Reservasi Asrama INSTIKI.
        </p>

    </div>

    <form method="POST" action="{{ route('register') }}">

        @csrf

        <!-- Nama -->
        <div>
            <x-input-label
                for="nama"
                :value="__('Nama Lengkap')"
                class="font-semibold text-slate-700"
            />

            <x-text-input
                id="nama"
                class="mt-2 block w-full"
                type="text"
                name="nama"
                :value="old('nama')"
                required
                autofocus
                autocomplete="name"
                placeholder="Masukkan nama lengkap"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('nama')"
            />
        </div>

        <!-- NIM -->
        <div class="mt-5">

            <x-input-label
                for="nim_nik"
                :value="__('NIM / NIK')"
                class="font-semibold text-slate-700"
            />

            <x-text-input
                id="nim_nik"
                class="mt-2 block w-full"
                type="text"
                name="nim_nik"
                :value="old('nim_nik')"
                required
                placeholder="Masukkan NIM atau NIK"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('nim_nik')"
            />

        </div>

        <!-- Telepon -->
        <div class="mt-5">

            <x-input-label
                for="no_telepon"
                :value="__('Nomor Telepon')"
                class="font-semibold text-slate-700"
            />

            <x-text-input
                id="no_telepon"
                class="mt-2 block w-full"
                type="text"
                name="no_telepon"
                :value="old('no_telepon')"
                required
                placeholder="08xxxxxxxxxx"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('no_telepon')"
            />

        </div>

        <!-- Email -->
        <div class="mt-5">

            <x-input-label
                for="email"
                :value="__('Email')"
                class="font-semibold text-slate-700"
            />

            <x-text-input
                id="email"
                class="mt-2 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                placeholder="Masukkan email aktif"
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
                class="font-semibold text-slate-700"
            />

            <x-text-input
                id="password"
                class="mt-2 block w-full"
                type="password"
                name="password"
                required
                placeholder="Minimal 8 karakter"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('password')"
            />

        </div>

        <!-- Konfirmasi Password -->
        <div class="mt-5">

            <x-input-label
                for="password_confirmation"
                :value="__('Konfirmasi Password')"
                class="font-semibold text-slate-700"
            />

            <x-text-input
                id="password_confirmation"
                class="mt-2 block w-full"
                type="password"
                name="password_confirmation"
                required
                placeholder="Ulangi password"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('password_confirmation')"
            />

        </div>

        <!-- Tombol -->
        <div class="mt-8">

            <x-primary-button
                class="flex w-full justify-center rounded-lg bg-[#ed1c24] py-3 text-base font-semibold hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:ring-[#ed1c24]">
                Daftar Akun
            </x-primary-button>

        </div>

        <!-- Login -->
        <div class="mt-6 border-t border-slate-200 pt-6 text-center">

            <p class="text-sm text-slate-600">

                Sudah memiliki akun?

                <a
                    href="{{ route('login') }}"
                    class="font-semibold text-[#ed1c24] hover:text-red-700 transition">
                    Masuk Sekarang
                </a>

            </p>

        </div>

    </form>

</div>

</x-guest-layout>