<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'FoodHub')</title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍔</text></svg>">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-pink-50 via-gray-50 to-orange-50 text-gray-900 min-h-screen flex items-center justify-center px-4 py-10">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="block text-center text-2xl font-extrabold text-pink-600 mb-6">🍔 FoodHub</a>

        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-7">
            @yield('content')
        </div>
    </div>
</body>
</html>
