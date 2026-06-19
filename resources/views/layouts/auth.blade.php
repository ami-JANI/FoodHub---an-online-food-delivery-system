<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FoodHub')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍔</text></svg>">
    @include('partials.theme-script')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-stone-100 via-stone-50 to-stone-100 dark:from-gray-950 dark:via-gray-950 dark:to-gray-900 text-gray-900 dark:text-gray-100 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full {{ $maxWidth ?? 'max-w-sm' }}">
        <div class="flex items-center justify-center gap-3 mb-6">
            <a href="{{ route('home') }}" class="text-2xl font-extrabold text-rose-950 dark:text-rose-400">🍔 FoodHub</a>
            @include('partials.theme-toggle', ['class' => 'text-sm bg-gray-900/5 dark:bg-white/10 hover:bg-gray-900/10 dark:hover:bg-white/20 transition w-8 h-8 rounded-full flex items-center justify-center'])
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-800 p-7">
            @yield('content')
        </div>
    </div>
</body>
</html>
