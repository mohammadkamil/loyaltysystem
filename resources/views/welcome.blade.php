<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Pixalink</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        body {
            font-family: 'Figtree', sans-serif;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-500 to-indigo-600 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-6 lg:px-20">
        <div class="bg-white shadow-lg rounded-lg overflow-hidden max-w-3xl mx-auto text-center p-10">
            <h1 class="text-4xl font-bold text-gray-800">Welcome to <span class="text-blue-600">Pixalink</span></h1>
            <p class="mt-2 text-gray-600">Manage your business efficiently with our powerful tools.</p>

            <div class="mt-6 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('filament.admin.auth.register') }}" 
                   class="px-6 py-3 bg-blue-600 text-white text-lg font-semibold rounded-lg shadow hover:bg-blue-700 transition">
                    Register
                </a>
                <a href="{{ route('filament.admin.auth.login') }}" 
                   class="px-6 py-3 bg-slate-600 text-white text-lg font-semibold rounded-lg shadow hover:bg-slate-800 transition">
                    Login
                </a>
            </div>

            {{-- <div class="mt-8">
                <img src="https://source.unsplash.com/600x300/?business,technology" alt="Business Image" class="rounded-lg shadow-lg w-full">
            </div> --}}
        </div>
    </div>
</body>
</html>
