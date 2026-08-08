<x-layouts.penghuni 
    title="Profil Saya - Asrama INSTIKI" 
    activeMenu="profil">

    <!-- Judul & Subjudul -->
    <div>
        <h1 class="text-2xl font-bold text-slate-900">Profil Saya</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola informasi akun, kata sandi, dan keamanan profil Anda</p>
    </div>

    <div class="space-y-6 max-w-4xl">
        <!-- Card 1: Informasi Profil -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Card 2: Perbarui Kata Sandi -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Card 3: Hapus Akun -->
        <div class="p-6 bg-white rounded-2xl border border-slate-200 shadow-sm">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>

</x-layouts.penghuni>