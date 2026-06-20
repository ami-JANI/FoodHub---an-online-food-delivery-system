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
<body class="bg-gray-50 dark:bg-gray-950 text-gray-900 dark:text-gray-100 antialiased">
    @php
        $navCuisines = \App\Models\Restaurant::where('is_approved', true)
            ->pluck('cuisine')->filter()
            ->flatMap(fn ($value) => array_map('trim', explode(',', $value)))
            ->unique()->sort()->values();
        $navCategories = \App\Models\Category::distinct()->orderBy('name')->pluck('name');
        $hasLocation = session('user_lat') !== null && session('user_lng') !== null;
    @endphp

    {{-- Row 1: utility bar — scrolls away (not sticky) --}}
    <div class="bg-stone-900 text-stone-300 text-xs sm:text-sm">
        <div class="max-w-6xl mx-auto px-4 py-2.5 flex items-center justify-between gap-2 sm:gap-4">
            <button type="button" id="location-pill" class="flex items-center gap-1.5 hover:text-white transition min-w-0 {{ $hasLocation ? '' : 'cursor-pointer' }}">
                <span class="shrink-0">📍</span>
                <span id="location-text" class="truncate max-w-[32vw] sm:max-w-xs">
                    {{ $hasLocation ? 'Detecting your location…' : 'Enter your location' }}
                </span>
                <span class="shrink-0">▾</span>
            </button>
            <div class="flex items-center gap-3 shrink-0">
                <a href="{{ route('track.index') }}" class="hover:text-white transition whitespace-nowrap">Track Order</a>
                <span class="w-px h-4 bg-stone-700"></span>
                @include('partials.theme-toggle', ['class' => 'hover:text-white transition text-base leading-none w-7 h-7 rounded-full flex items-center justify-center hover:bg-white/10'])
            </div>
        </div>
    </div>

    {{-- Rows 2 (+3): pinned to top once row 1 scrolls past --}}
    <div class="sticky top-0 z-20 shadow-md">
        <header class="bg-rose-950 text-white">
            <div class="max-w-6xl mx-auto px-4 py-3 flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight flex items-center gap-1.5 shrink-0">
                    <span class="text-2xl">🍔</span> <span class="hidden sm:inline">FoodHub</span>
                </a>

                <nav class="hidden lg:flex items-center gap-5 text-sm font-medium ml-2 shrink-0">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') && ! request()->query() ? 'text-amber-400' : 'hover:text-amber-300' }} transition">Home</a>

                    <div class="relative group">
                        <button type="button" class="flex items-center gap-1 hover:text-amber-300 transition">Categories <span class="text-xs">▾</span></button>
                        <div class="absolute left-0 top-full hidden group-hover:block bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-lg shadow-lg border border-gray-100 dark:border-gray-800 py-2 w-48 z-30">
                            @forelse ($navCategories as $category)
                                <a href="{{ route('home', ['menu_category' => $category]) }}" class="block px-4 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">{{ $category }}</a>
                            @empty
                                <span class="block px-4 py-1.5 text-sm text-gray-400">No categories yet</span>
                            @endforelse
                        </div>
                    </div>

                    <div class="relative group">
                        <button type="button" class="flex items-center gap-1 hover:text-amber-300 transition">Cuisines <span class="text-xs">▾</span></button>
                        <div class="absolute left-0 top-full hidden group-hover:block bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-200 rounded-lg shadow-lg border border-gray-100 dark:border-gray-800 py-2 w-48 z-30">
                            @forelse ($navCuisines as $cuisine)
                                <a href="{{ route('home', ['cuisine' => $cuisine]) }}" class="block px-4 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800">{{ $cuisine }}</a>
                            @empty
                                <span class="block px-4 py-1.5 text-sm text-gray-400">No cuisines yet</span>
                            @endforelse
                        </div>
                    </div>

                    <a href="{{ route('home') }}" class="hover:text-amber-300 transition">Restaurants</a>
                </nav>

                <form action="{{ route('home') }}" method="GET" class="flex-1 max-w-xl mx-2">
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔎</span>
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search foods and restaurants…."
                            class="w-full bg-white text-gray-800 placeholder:text-gray-400 rounded-full pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400">
                    </div>
                </form>

                <div class="flex items-center gap-2.5 shrink-0">
                    @php($cartCount = collect(session('cart', []))->sum())
                    <a href="{{ route('cart.index') }}" class="relative flex items-center gap-1.5 bg-white/15 hover:bg-white/25 transition px-3.5 py-2 rounded-full text-sm font-semibold">
                        🛒 <span class="hidden sm:inline">Cart</span>
                        @if ($cartCount > 0)
                            <span class="absolute -top-1.5 -right-1.5 bg-amber-500 text-white text-[10px] font-bold w-5 h-5 rounded-full flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </a>

                    @auth
                        <a href="{{ route('account.show') }}" class="hidden sm:inline text-sm font-medium hover:underline">Hi, {{ auth()->user()->name }}</a>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="text-sm bg-white/15 hover:bg-white/25 transition px-3.5 py-2 rounded-full font-semibold">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm bg-white text-rose-950 hover:bg-rose-50 transition px-3.5 py-2 rounded-full font-semibold whitespace-nowrap">
                            👤 Sign In
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        @hasSection('filterbar')
            <div class="bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
                <div class="max-w-6xl mx-auto px-4 py-2.5 overflow-x-auto">
                    @yield('filterbar')
                </div>
            </div>
        @endif
    </div>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 px-4 py-2.5 text-sm font-medium">
                ✅ {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    <script>
        (function () {
            var textEl = document.getElementById('location-text');
            var pill = document.getElementById('location-pill');
            var hasLocation = @json($hasLocation);
            var lat = @json(session('user_lat'));
            var lng = @json(session('user_lng'));

            function requestLocation() {
                if (!navigator.geolocation) {
                    textEl.textContent = 'Location not supported';
                    return;
                }

                textEl.textContent = 'Requesting location…';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        fetch('{{ route('location.update') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                latitude: position.coords.latitude,
                                longitude: position.coords.longitude,
                            }),
                        }).then(function () {
                            window.location.reload();
                        });
                    },
                    function () {
                        textEl.textContent = 'Location unavailable — tap to retry';
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            }

            if (hasLocation && lat !== null && lng !== null) {
                var cacheKey = 'locationName:' + lat.toFixed(3) + ',' + lng.toFixed(3);
                var cached = sessionStorage.getItem(cacheKey);

                if (cached) {
                    textEl.textContent = cached;
                } else {
                    fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=14&addressdetails=1')
                        .then(function (response) { return response.json(); })
                        .then(function (data) {
                            var addr = data.address || {};
                            var place = addr.suburb || addr.neighbourhood || addr.city_district
                                || addr.city || addr.town || addr.village || 'Your current location';
                            var area = addr.city || addr.town || addr.state || '';
                            var label = (area && area !== place) ? (place + ', ' + area) : place;

                            textEl.textContent = label;
                            sessionStorage.setItem(cacheKey, label);
                        })
                        .catch(function () {
                            textEl.textContent = 'Your current location';
                        });
                }
            } else {
                pill.addEventListener('click', requestLocation);
                requestLocation();
            }
        })();
    </script>

    <footer class="bg-stone-900 text-stone-400 mt-12">
        <div class="max-w-6xl mx-auto px-4 py-10 grid sm:grid-cols-3 gap-8 text-sm">
            <div>
                <p class="text-white font-bold text-lg mb-2">🍔 FoodHub</p>
                <p class="text-stone-500">Order food from your favorite local restaurants, delivered fast.</p>
            </div>
            <div>
                <p class="text-white font-semibold mb-2">Company</p>
                <ul class="space-y-1 text-stone-500">
                    <li><a href="{{ route('home') }}" class="hover:text-white transition">Restaurants</a></li>
                    <li><a href="{{ route('restaurant.register') }}" class="hover:text-white transition">Become a partner</a></li>
                    <li><a href="{{ route('rider.register') }}" class="hover:text-white transition">🛵 Become a rider</a></li>
                    <li><a href="{{ route('rider.login') }}" class="hover:text-white transition">Rider sign in</a></li>
                    <li><a href="{{ route('admin.login') }}" class="hover:text-white transition">Admin</a></li>
                </ul>
            </div>
            <div>
                <p class="text-white font-semibold mb-2">Web Lab Project</p>
                <p class="text-stone-500">Built with Laravel &amp; Tailwind CSS as a university Web Lab assignment.</p>
            </div>
        </div>
        <div class="border-t border-stone-800 text-center text-xs text-stone-500 py-4">
            &copy; {{ date('Y') }} FoodHub &middot; Web Lab Project
        </div>
    </footer>
</body>
</html>
