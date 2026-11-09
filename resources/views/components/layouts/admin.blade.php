@props([
    'title' => 'Admin Asrama INSTIKI',
    'activeMenu' => '',
    'searchRoute' => null,
    'searchPlaceholder' => 'Cari...'
])

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        [x-cloak] { display: none !important; }
        .adm-sidebar { position: fixed; top: 0; bottom: 0; left: 0; z-index: 50; width: 16rem; transform: translateX(-100%); transition: transform .2s ease; }
        .adm-sidebar.is-open { transform: translateX(0); box-shadow: 0 25px 50px -12px rgba(0,0,0,.25); }
        .adm-overlay { position: fixed; inset: 0; z-index: 40; background: rgba(15,23,42,.5); }
        @media (min-width: 768px) {
            .adm-wrap { display: contents; }
            .adm-sidebar { position: sticky; top: 0; height: 100vh; z-index: auto; transform: none; box-shadow: none; flex-shrink: 0; }
            .adm-only-mobile { display: none !important; }
        }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased selection:bg-[#ed1c24] selection:text-white" {{ $attributes }}>
    <div class="flex min-h-screen">

        <!-- Sidebar Component -->
        <x-admin.sidebar :active="$activeMenu" />

        <!-- Area Konten Utama -->
        <div class="flex min-w-0 flex-1 flex-col">

            <!-- Navbar Component -->
            <x-admin.navbar
                :pageTitle="\Illuminate\Support\Str::before($title, ' - ')"
                :searchRoute="$searchRoute"
                :placeholder="$searchPlaceholder" />

            <!-- Body Content -->
            <main class="min-w-0 flex-1 space-y-6 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>