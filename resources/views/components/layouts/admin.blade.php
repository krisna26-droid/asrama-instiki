@props([
    'title' => 'Admin Asrama INSTIKI',
    'activeMenu' => '',
    'searchRoute' => null,
    'searchPlaceholder' => 'Cari...'
])

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" {{ $attributes }}>
    <div class="min-h-screen flex">

        <!-- Sidebar Component -->
        <x-admin.sidebar :active="$activeMenu" />

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Navbar Component -->
            <x-admin.navbar :searchRoute="$searchRoute" :placeholder="$searchPlaceholder" />

            <!-- Body Content -->
            <main class="p-6 sm:p-8 space-y-6 flex-1 overflow-y-auto">
                {{ $slot }}
            </main>
        </div>

    </div>
</body>
</html>