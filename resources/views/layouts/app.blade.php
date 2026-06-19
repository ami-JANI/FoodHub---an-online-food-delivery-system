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
    <div class="bg-stone-900 text-stone-300 text-sm">
        <div class="max-w-6xl mx-auto px-4 py-2.5 flex items-center justify-between gap-4">
            <span class="hidden sm:inline text-stone-400">Delivering happiness across Dhaka 🇧🇩</span>
            <div class="flex items-center gap-5 ml-auto">
                <a href="{{ route('restaurant.register') }}" class="hover:text-white transition">🏪 Add your restaurant</a>
                <a href="{{ route('restaurant.login') }}" class="hover:text-white transition">Partner sign in</a>
                <span class="w-px h-4 bg-stone-700 hidden sm:inline"></span>
                @include('partials.theme-toggle', ['class' => 'hover:text-white transition text-base leading-none'])
            </div>
        </div>
    </div>

    <header class="bg-rose-950 text-white sticky top-0 z-10 shadow-md">
        <div class="max-w-6xl mx-auto px-4 py-3.5 flex items-center justify-between gap-4">
            <a href="{{ route('home') }}" class="text-xl font-extrabold tracking-tight flex items-center gap-1.5 shrink-0">
                <span class="text-2xl">🍔</span> FoodHub
            </a>

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
                    <a href="{{ route('login') }}" class="text-sm bg-white text-rose-950 hover:bg-rose-50 transition px-3.5 py-2 rounded-full font-semibold">
                        Sign in
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-6">
        @if (session('status'))
            <div class="mb-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800 px-4 py-2.5 text-sm font-medium">
                ✅ {{ session('status') }}
            </div>
        @endif

        @yield('content')
    </main>

    @unless (session('user_lat'))
        <script>
            (function () {
                if (!navigator.geolocation) {
                    return;
                }

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
                        // Permission denied or unavailable — silently fall back to showing all restaurants.
                    },
                    { enableHighAccuracy: true, timeout: 8000 }
                );
            })();
        </script>
    @endunless

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
