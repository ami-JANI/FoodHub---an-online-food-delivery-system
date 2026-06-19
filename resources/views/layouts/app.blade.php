<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FoodHub')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900">
    <header class="bg-pink-600 text-white sticky top-0 z-10 shadow">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-bold">🍔 FoodHub</a>
            <a href="{{ route('cart.index') }}" class="flex items-center gap-1 bg-pink-700 hover:bg-pink-800 px-3 py-1.5 rounded-full text-sm font-medium">
                🛒 Cart
            </a>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded bg-green-100 text-green-800 px-4 py-2 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 py-8">
        FoodHub &middot; Web Lab Project
    </footer>
</body>
</html>
