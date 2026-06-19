<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FoodHub')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍔</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 text-gray-900">
    <header class="bg-gray-900 text-white sticky top-0 z-10 shadow-md">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <span class="text-xl font-extrabold flex items-center gap-2">
                🍔 FoodHub
                <span class="text-xs font-semibold text-gray-300 bg-white/10 px-2 py-0.5 rounded-full">{{ $portal ?? '' }}</span>
            </span>
            @yield('header-actions')
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 py-8">
        FoodHub &middot; Web Lab Project
    </footer>
</body>
</html>
