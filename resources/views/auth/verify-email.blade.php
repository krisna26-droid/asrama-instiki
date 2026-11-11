<x-guest-layout>
    <div class="space-y-4 rounded-xl border border-slate-200 bg-white p-6 text-center shadow-sm">
        <h1 class="text-lg font-bold text-slate-900">Verifikasi email tidak diperlukan</h1>
        <p class="text-sm leading-6 text-slate-600">Akun asrama tidak menggunakan verifikasi email. Silakan lanjutkan melalui halaman login.</p>
        <a href="{{ route('login') }}" class="inline-flex rounded-lg bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-700">Kembali ke login</a>
    </div>
</x-guest-layout>
