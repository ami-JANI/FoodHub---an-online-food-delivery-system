@php
    $mapId = $mapId ?? 'map-display';
    $lat = $lat ?? 23.8103;
    $lng = $lng ?? 90.4125;
    $markerLabel = $markerLabel ?? 'Location';
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div id="{{ $mapId }}" class="h-56 w-full rounded-lg border border-gray-200 dark:border-gray-700 z-0"></div>

<script>
    (function () {
        var map = L.map('{{ $mapId }}').setView([{{ $lat }}, {{ $lng }}], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        L.marker([{{ $lat }}, {{ $lng }}]).addTo(map).bindPopup({!! json_encode($markerLabel) !!});

        window['{{ $mapId }}_map'] = map;
        window['{{ $mapId }}_secondaryMarker'] = null;
    })();
</script>
