@extends('layouts.app')

@section('title', 'FoodHub - Order food online')

@section('content')
    <div class="bg-gradient-to-br from-stone-900 to-rose-950 rounded-2xl px-6 py-10 sm:px-10 sm:py-14 text-white mb-8 shadow-lg">
        <h1 class="text-2xl sm:text-3xl font-extrabold mb-2">Hungry? We've got you covered.</h1>
        <p class="text-stone-300 sm:text-lg mb-5">Order from {{ $restaurants->count() }} restaurants near you — delivered fast, fresh, and hot.</p>
        <button type="button" id="location-pill"
            class="flex items-center gap-2 bg-white rounded-full px-4 py-2.5 max-w-md shadow text-left {{ $hasLocation ? '' : 'hover:bg-gray-50 transition' }}">
            <span class="text-gray-400">📍</span>
            <span id="location-text" class="text-gray-500 text-sm">
                {{ $hasLocation ? 'Detecting your location…' : 'Enter your location' }}
            </span>
        </button>
    </div>

    <script>
        (function () {
            var textEl = document.getElementById('location-text');
            var pill = document.getElementById('location-pill');
            var hasLocation = @json($hasLocation);
            var lat = @json($userLat);
            var lng = @json($userLng);

            function requestLocation() {
                if (!navigator.geolocation) {
                    textEl.textContent = 'Location not supported on this browser';
                    return;
                }

                textEl.textContent = 'Requesting your location…';

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
                        textEl.textContent = 'Location unavailable — tap to try again';
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
            }
        })();
    </script>

    @unless ($hasLocation)
        <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm mb-6">
            <span class="text-lg shrink-0">📍</span>
            <p>Showing all restaurants. Enable location access in your browser to see restaurants within 5km of you, with accurate delivery fees.</p>
        </div>
    @endunless

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">{{ $hasLocation ? 'Restaurants within 5km' : 'Restaurants near you' }}</h2>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $restaurants->count() }} results</span>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php($gradients = ['from-stone-200 to-rose-100', 'from-amber-100 to-stone-200', 'from-rose-100 to-stone-200', 'from-stone-200 to-amber-100'])
        @foreach ($restaurants as $restaurant)
            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
               class="group block bg-white dark:bg-gray-900 rounded-2xl shadow-sm hover:shadow-xl transition-all hover:-translate-y-0.5 overflow-hidden border border-gray-100 dark:border-gray-800">
                <div class="h-36 bg-gradient-to-br {{ $gradients[$loop->index % count($gradients)] }} flex items-center justify-center text-stone-500 text-4xl relative overflow-hidden">
                    @if ($restaurant->cover_image)
                        <img src="{{ asset('uploads/' . $restaurant->cover_image) }}" alt="{{ $restaurant->name }}" class="absolute inset-0 w-full h-full object-cover">
                    @else
                        🍽️
                    @endif
                    @unless ($restaurant->is_open)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-sm font-semibold bg-white text-gray-900 px-3 py-1 rounded-full">Closed</span>
                        </div>
                    @endunless
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold text-lg group-hover:text-rose-800 dark:group-hover:text-rose-400 transition truncate">{{ $restaurant->name }}</h3>
                        <span class="shrink-0 text-sm bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-md font-semibold">★ {{ $restaurant->rating }}</span>
                    </div>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $restaurant->cuisine }}</p>
                    <div class="flex items-center gap-3 text-xs text-gray-400 dark:text-gray-500 mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <span>⏱ {{ $restaurant->delivery_time }}</span>
                        <span>🚲 Tk {{ number_format($restaurant->computed_delivery_fee, 0) }} delivery</span>
                        @if ($restaurant->distance_km !== null)
                            <span>📍 {{ number_format($restaurant->distance_km, 1) }} km</span>
                        @endif
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
