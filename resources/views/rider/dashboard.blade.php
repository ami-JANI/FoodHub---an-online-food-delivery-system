@extends('layouts.dashboard', ['portal' => 'Rider'])

@section('title', 'Rider Dashboard - FoodHub')

@section('header-actions')
    <form action="{{ route('rider.logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Logout</button>
    </form>
@endsection

@section('content')
    @unless ($rider->is_approved)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⏳</span>
            <p>Your rider application is pending admin approval. You'll be able to accept deliveries once an admin approves you and sets your hourly wage.</p>
        </div>
    @endunless

    @if ($adminCancelledOrders->isNotEmpty())
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl px-4 py-4">
            <div class="flex items-start gap-3 mb-2">
                <span class="text-lg shrink-0">⚠️</span>
                <p class="font-semibold text-red-800 dark:text-red-300 text-sm">Deliveries cancelled by FoodHub</p>
            </div>
            <ul class="space-y-1.5 pl-1">
                @foreach ($adminCancelledOrders as $cancelled)
                    <li class="text-sm text-red-700 dark:text-red-400">
                        <span class="font-semibold">Order #{{ $cancelled->id }}</span> ({{ $cancelled->restaurant->name }}, {{ $cancelled->created_at->format('d M, h:i A') }}) — {{ \App\Models\Order::ADMIN_CANCEL_PARTNER_MESSAGE }}
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 flex items-center justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold">Hi, {{ $rider->name }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $rider->vehicle_type }} &middot; {{ $rider->educational_qualification }}</p>
        </div>
        @if ($rider->is_approved)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm px-4 py-2.5">
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Hourly wage</p>
                <p class="text-lg font-bold">Tk {{ number_format($rider->hourly_wage, 0) }}/hr</p>
            </div>
        @endif
    </div>

    @if ($rider->is_approved)
        <div id="location-banner" class="flex items-center gap-3 bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-3 text-sm mb-6">
            <span class="text-lg shrink-0">📍</span>
            <p id="location-status" class="text-gray-600 dark:text-gray-300">
                {{ $hasLocation ? 'Showing pickup requests within 2km of your last known location.' : 'Detecting your location to find nearby pickup requests…' }}
            </p>
        </div>

        <h2 class="text-lg font-bold mb-3">Nearby pickup requests <span class="text-gray-400 dark:text-gray-500 font-normal text-sm">(within 2km)</span></h2>
        @if (! $hasLocation)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
                Waiting for your location — allow location access in your browser.
            </div>
        @elseif ($nearbyOrders->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
                No pickup requests near you right now.
            </div>
        @else
            <div class="space-y-3 mb-8">
                @foreach ($nearbyOrders as $order)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold truncate">{{ $order->restaurant->name }} <span class="text-gray-400 dark:text-gray-500 font-normal">— Order #{{ $order->id }}</span></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">Pickup: {{ $order->restaurant->address_line }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">Deliver to: {{ $order->address_line }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">📍 {{ number_format($order->pickup_distance_km, 1) }} km away &middot; {{ $order->items->count() }} items &middot; Tk {{ number_format($order->total, 0) }}</p>
                            @if ($order->route_distance_km !== null)
                                <p class="text-xs font-semibold text-green-700 dark:text-green-400 mt-0.5">💸 Earn ~Tk {{ number_format($order->rider_earning, 0) }} &middot; {{ number_format($order->route_distance_km, 1) }} km route</p>
                            @endif
                        </div>
                        <form action="{{ route('rider.orders.accept', $order) }}" method="POST" class="shrink-0">
                            @csrf
                            <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-2 rounded-full transition">Accept</button>
                        </form>
                    </div>
                @endforeach
            </div>
        @endif

        <h2 class="text-lg font-bold mb-3">Active deliveries</h2>
        @if ($activeDeliveries->isEmpty())
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
                You have no active deliveries.
            </div>
        @else
            <div class="space-y-3 mb-8">
                @foreach ($activeDeliveries as $order)
                    <a href="{{ route('rider.orders.show', $order) }}" class="block bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center justify-between gap-3 hover:border-rose-400 transition">
                        <div class="min-w-0">
                            <p class="font-semibold truncate">{{ $order->restaurant->name }} <span class="text-gray-400 dark:text-gray-500 font-normal">— Order #{{ $order->id }}</span></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 truncate">Deliver to: {{ $order->address_line }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">📞 {{ $order->phone }} &middot; Tk {{ number_format($order->total, 0) }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2.5 py-1 rounded-full whitespace-nowrap">{{ $order->statusLabel() }}</span>
                    </a>
                @endforeach
            </div>
        @endif

        @if ($pastDeliveries->isNotEmpty())
            <h2 class="text-lg font-bold mb-3">Past deliveries</h2>
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800">
                @foreach ($pastDeliveries as $order)
                    <div class="p-4 flex items-center justify-between gap-3">
                        <p class="font-medium">{{ $order->restaurant->name }} <span class="text-gray-400 dark:text-gray-500 font-normal">— Order #{{ $order->id }}</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->delivered_at?->format('d M Y, h:i A') }}</p>
                    </div>
                @endforeach
            </div>
        @endif

        <script>
            (function () {
                var hasLocation = @json($hasLocation);

                function updateLocation(reloadAfter) {
                    if (!navigator.geolocation) {
                        document.getElementById('location-status').textContent = 'Geolocation is not supported by this browser.';
                        return;
                    }

                    navigator.geolocation.getCurrentPosition(
                        function (position) {
                            fetch('{{ route('rider.location.update') }}', {
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
                                if (reloadAfter) {
                                    window.location.reload();
                                } else {
                                    document.getElementById('location-status').textContent = 'Location refreshed.';
                                }
                            });
                        },
                        function () {
                            document.getElementById('location-status').textContent = 'Location permission denied — enable it to see nearby pickup requests.';
                        },
                        { enableHighAccuracy: true, timeout: 8000 }
                    );
                }

                // Only auto-reload the very first time we capture a location, so
                // the page re-renders with nearby orders. After that, refresh
                // silently (no reload loop) every time this script runs again.
                updateLocation(!hasLocation);

                // While delivering, keep broadcasting live location every 15s
                // so the customer's tracking page can follow along on the map.
                var hasActiveDeliveries = @json($activeDeliveries->isNotEmpty());
                if (hasActiveDeliveries) {
                    setInterval(function () { updateLocation(false); }, 15000);
                }
            })();
        </script>
    @endif
@endsection
