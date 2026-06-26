@extends('layouts.app')

@section('title', 'Set your location - FoodHub')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; Back home</a>

        <h1 class="text-2xl font-bold mt-3 mb-1">Set your location</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6 text-sm">Search for a place, use your current location, or click anywhere on the map to drop a pin — even somewhere you're not. This location will be used to show nearby restaurants and at checkout.</p>

        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-2 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('location.set.store') }}" method="POST" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 space-y-4">
            @csrf

            <div>
                <label class="text-sm font-medium block mb-1">Search for a place</label>
                <div class="flex items-center gap-2">
                    <input type="text" id="place-search" placeholder="e.g. Dhanmondi, Dhaka"
                        class="flex-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                    <button type="button" id="place-search-btn" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-lg transition whitespace-nowrap">Search</button>
                </div>
                <p id="place-search-status" class="text-xs text-gray-400 dark:text-gray-500 mt-1"></p>
            </div>

            @include('partials.map-picker', [
                'mapId' => 'set-location-map',
                'initialLat' => $lat ?? 23.8103,
                'initialLng' => $lng ?? 90.4125,
            ])

            <div>
                <label class="text-sm font-medium block mb-1">Address / area label (optional)</label>
                <input type="text" name="label" id="location-label" value="{{ old('label', $label) }}" maxlength="255" placeholder="e.g. Home — House 12, Road 3, Dhanmondi"
                    class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">Use this location</button>
        </form>
    </div>

    <script>
        (function () {
            var input = document.getElementById('place-search');
            var btn = document.getElementById('place-search-btn');
            var statusEl = document.getElementById('place-search-status');
            var labelEl = document.getElementById('location-label');

            function search() {
                var query = input.value.trim();
                if (!query) return;

                statusEl.textContent = 'Searching…';
                fetch('https://nominatim.openstreetmap.org/search?format=json&limit=1&q=' + encodeURIComponent(query))
                    .then(function (r) { return r.json(); })
                    .then(function (results) {
                        if (!results.length) {
                            statusEl.textContent = 'No place found. Try a different search, or click the map.';
                            return;
                        }
                        var place = results[0];
                        var lat = parseFloat(place.lat);
                        var lng = parseFloat(place.lon);
                        var setPin = window['set-location-map_setPin'];
                        if (setPin) {
                            setPin(lat, lng, 16);
                        }
                        statusEl.textContent = 'Found: ' + place.display_name;
                        if (!labelEl.value) {
                            labelEl.value = place.display_name;
                        }
                    })
                    .catch(function () {
                        statusEl.textContent = 'Search failed. Please try again.';
                    });
            }

            btn.addEventListener('click', search);
            input.addEventListener('keydown', function (event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    search();
                }
            });
        })();
    </script>
@endsection
