<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'FoodHub')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍔</text></svg>">
    @include('partials.theme-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-950 text-gray-900 dark:text-gray-100">
    <header class="bg-stone-900 text-white sticky top-0 z-10 shadow-md">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <span class="text-xl font-extrabold flex items-center gap-2">
                🍔 FoodHub
                <span class="text-xs font-semibold text-stone-300 bg-white/10 px-2 py-0.5 rounded-full">{{ $portal ?? '' }}</span>
            </span>
            <div class="flex items-center gap-2.5">
                @include('partials.theme-toggle')
                @yield('header-actions')
            </div>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 px-4 py-2.5 text-sm font-medium">
                ✅ {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-xs text-gray-400 dark:text-gray-600 py-8">
        &copy; {{ date('Y') }} FoodHub. All rights reserved.
    </footer>
</body>
</html>
