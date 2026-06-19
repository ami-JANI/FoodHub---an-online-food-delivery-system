@php
    $mapId = $mapId ?? 'map-picker';
    $latName = $latName ?? 'latitude';
    $lngName = $lngName ?? 'longitude';
    $initialLat = $initialLat ?? 23.8103;
    $initialLng = $initialLng ?? 90.4125;
    $hasInitialPin = isset($initialLat, $initialLng) && $initialLat && $initialLng;
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div>
    <div class="flex items-center justify-between mb-2">
        <label class="text-sm font-medium">Pin your location on the map</label>
        <button type="button" id="{{ $mapId }}-locate-btn"
            class="text-xs font-semibold text-rose-800 dark:text-rose-400 hover:underline">
            📍 Use my current location
        </button>
    </div>

    <div id="{{ $mapId }}" class="h-64 w-full rounded-lg border border-gray-200 dark:border-gray-700 z-0"></div>

    <p id="{{ $mapId }}-status" class="text-xs text-gray-500 dark:text-gray-400 mt-1.5">
        Click anywhere on the map to drop a pin, or use your current location.
    </p>

    <input type="hidden" name="{{ $latName }}" id="{{ $mapId }}-lat" value="{{ $initialLat }}">
    <input type="hidden" name="{{ $lngName }}" id="{{ $mapId }}-lng" value="{{ $initialLng }}">
</div>

<script>
    (function () {
        var mapId = '{{ $mapId }}';
        var initialLat = {{ $initialLat }};
        var initialLng = {{ $initialLng }};
        var hasInitialPin = {{ $hasInitialPin ? 'true' : 'false' }};

        var latInput = document.getElementById(mapId + '-lat');
        var lngInput = document.getElementById(mapId + '-lng');
        var statusEl = document.getElementById(mapId + '-status');

        var map = L.map(mapId).setView([initialLat, initialLng], hasInitialPin ? 15 : 12);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        var marker = hasInitialPin
            ? L.marker([initialLat, initialLng], { draggable: true }).addTo(map)
            : null;

        function setPin(lat, lng, zoom) {
            latInput.value = lat;
            lngInput.value = lng;

            if (marker) {
                marker.setLatLng([lat, lng]);
            } else {
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', function () {
                    var pos = marker.getLatLng();
                    setPin(pos.lat, pos.lng);
                });
            }

            map.setView([lat, lng], zoom || map.getZoom());
            statusEl.textContent = 'Pinned location: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
        }

        if (marker) {
            marker.on('dragend', function () {
                var pos = marker.getLatLng();
                setPin(pos.lat, pos.lng);
            });
        }

        map.on('click', function (event) {
            setPin(event.latlng.lat, event.latlng.lng, 16);
        });

        document.getElementById(mapId + '-locate-btn').addEventListener('click', function () {
            requestCurrentLocation();
        });

        function requestCurrentLocation() {
            if (!navigator.geolocation) {
                statusEl.textContent = 'Geolocation is not supported by this browser.';
                return;
            }

            statusEl.textContent = 'Requesting your location…';

            navigator.geolocation.getCurrentPosition(
                function (position) {
                    setPin(position.coords.latitude, position.coords.longitude, 16);
                },
                function () {
                    statusEl.textContent = 'Location permission denied. You can still click the map to pin manually.';
                },
                { enableHighAccuracy: true, timeout: 8000 }
            );
        }

        // Ask for location on first load if no pin is set yet — this is what triggers
        // the browser's permission popup automatically.
        if (!hasInitialPin) {
            requestCurrentLocation();
        }
    })();
</script>
