<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Admin Keuangan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="min-h-screen flex flex-col">
        <!-- Navbar -->
        <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
            <h1 class="font-bold text-xl text-gray-800">Panel Admin Keuangan INSTIKI</h1>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600">Halo, {{ auth()->user()->nama }}</span>
                <!-- Tombol Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700 transition">
                        Log Out
                    </button>
                </form>
            </div>
        </nav>

        <!-- Content -->
        <main class="max-w-7xl mx-auto px-6 py-8 w-full">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Selamat Datang di Panel Keuangan</h3>
                <p class="text-gray-600">Dari halaman ini, Admin Keuangan dapat memverifikasi bukti pembayaran sewa asrama dari mahasiswa.</p>
            </div>
        </main>
    </div>
</body>
</html>