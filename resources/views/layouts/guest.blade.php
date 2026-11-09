<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-slate-900 font-sans antialiased selection:bg-blue-600 selection:text-white">
    <div class="absolute inset-0 z-0" style="background-image: url('{{ asset('image/asrama-bg.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;"></div>
    <div class="fixed inset-0 bg-slate-950/65"></div>
    <main class="relative flex min-h-screen items-center justify-center px-4 py-10">

        {{ $slot }}

    </main>

</body>
</html>
