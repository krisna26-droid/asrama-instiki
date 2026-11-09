@props([
    'title' => '',
    'maxWidth' => '28rem',
    'open' => false,
])

@once
    <style>
        .adm-modal { position: fixed; inset: 0; z-index: 100; display: flex; align-items: center; justify-content: center; padding: 1rem; }
        .adm-modal-backdrop { position: absolute; inset: 0; background: rgba(15, 23, 42, .55); backdrop-filter: blur(2px); }
        .adm-modal-panel { position: relative; width: 100%; max-height: 90vh; overflow-y: auto; background: #fff; border-radius: .75rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, .35); }
    </style>
@endonce

<div x-data="{ open: {{ $open ? 'true' : 'false' }} }" @keydown.escape.window="open = false" class="inline-block">

    <!-- Tombol pemicu -->
    <span @click="open = true">{{ $trigger }}</span>

    <!-- Pop up (dipindah ke body supaya tidak terpotong tabel) -->
    <template x-teleport="body">
        <div x-cloak x-show="open" x-transition.opacity class="adm-modal" role="dialog" aria-modal="true">
            <div class="adm-modal-backdrop" @click="open = false"></div>

            <div class="adm-modal-panel" style="max-width: {{ $maxWidth }};">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-base font-bold text-slate-900">{{ $title }}</h3>
                    <button type="button" @click="open = false" class="rounded p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600" aria-label="Tutup">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" d="m6 6 12 12M18 6 6 18"/></svg>
                    </button>
                </div>

                <div class="px-6 py-5">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </template>
</div>