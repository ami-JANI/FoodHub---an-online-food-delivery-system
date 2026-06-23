@php
    $mapId = $mapId ?? 'route-map';
    $points = $points ?? [];
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<div id="{{ $mapId }}" class="h-64 w-full rounded-lg border border-gray-200 dark:border-gray-700 z-0"></div>

<script>
    (function () {
        var points = {!! json_encode($points) !!};
        var map = L.map('{{ $mapId }}');

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 19,
        }).addTo(map);

        var latlngs = [];
        points.forEach(function (p) {
            var latlng = [p.lat, p.lng];
            latlngs.push(latlng);
            L.circleMarker(latlng, {
                radius: 9,
                color: '#fff',
                weight: 2,
                fillColor: p.color || '#881337',
                fillOpacity: 1,
            }).addTo(map).bindPopup(p.label);
        });

        if (latlngs.length > 1) {
            L.polyline(latlngs, { color: '#881337', weight: 4, opacity: 0.7 }).addTo(map);
            map.fitBounds(latlngs, { padding: [40, 40] });
        } else if (latlngs.length === 1) {
            map.setView(latlngs[0], 15);
        } else {
            map.setView([23.8103, 90.4125], 12);
        }

        window['{{ $mapId }}_map'] = map;
    })();
</script>
