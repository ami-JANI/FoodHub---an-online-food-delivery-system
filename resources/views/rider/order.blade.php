@extends('layouts.dashboard', ['portal' => 'Rider'])

@section('title', 'Order #' . $order->id . ' - FoodHub')

@section('header-actions')
    <a href="{{ route('rider.dashboard') }}" class="text-sm font-medium hover:underline">&larr; Dashboard</a>
@endsection

@section('content')
    <div class="flex items-center justify-between gap-3 mb-6 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold">Order #{{ $order->id }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $order->restaurant->name }} &middot; {{ $order->statusLabel() }}</p>
        </div>

        <div class="flex items-center gap-2">
            @if ($order->status === \App\Models\Order::RIDER_ASSIGNED)
                <form action="{{ route('rider.orders.arrived', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition">I've arrived at the restaurant</button>
                </form>
            @elseif ($order->status === \App\Models\Order::RIDER_ARRIVED)
                <form action="{{ route('rider.orders.picked-up', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition">Picked up the order</button>
                </form>
            @elseif ($order->status === \App\Models\Order::PICKED_UP)
                <form action="{{ route('rider.orders.on-the-way', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition">On the way to customer</button>
                </form>
            @elseif ($order->status === \App\Models\Order::ON_THE_WAY)
                <form action="{{ route('rider.orders.complete', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm font-semibold bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-full transition">Mark delivered</button>
                </form>
            @endif
        </div>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <h2 class="font-bold mb-3">Delivery route</h2>
                @php
                    $routePoints = [];
                    if ($riderLat && $riderLng) {
                        $routePoints[] = ['lat' => (float) $riderLat, 'lng' => (float) $riderLng, 'label' => 'You (rider)', 'color' => '#2563eb'];
                    }
                    $routePoints[] = ['lat' => (float) $order->restaurant->latitude, 'lng' => (float) $order->restaurant->longitude, 'label' => $order->restaurant->name . ' — pickup', 'color' => '#ea580c'];
                    $routePoints[] = ['lat' => (float) $order->latitude, 'lng' => (float) $order->longitude, 'label' => 'Customer — drop-off', 'color' => '#16a34a'];
                @endphp
                @include('partials.route-map', ['mapId' => 'delivery-route-map', 'points' => $routePoints])

                <div class="flex items-center gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#2563eb"></span> You</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#ea580c"></span> Restaurant (pickup)</span>
                    <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full inline-block" style="background:#16a34a"></span> Customer (drop-off)</span>
                </div>

                <div class="flex items-center justify-between mt-3 gap-3 flex-wrap">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-300">📍 {{ $order->address_line }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-300">📞 {{ $order->phone }}</p>
                    </div>
                    <a href="https://www.google.com/maps/dir/?api=1&destination={{ $order->latitude }},{{ $order->longitude }}"
                       target="_blank" rel="noopener"
                       class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition whitespace-nowrap">
                        🧭 Navigate
                    </a>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <h2 class="font-bold mb-3">Order items</h2>
                <div class="space-y-1">
                    @foreach ($order->items as $item)
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                            <span>{{ $item->quantity }}× {{ $item->name }}</span>
                            <span>Tk {{ number_format($item->price * $item->quantity, 0) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between font-bold mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <span>Total</span>
                    <span>Tk {{ number_format($order->total, 0) }}</span>
                </div>
            </div>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex flex-col h-[28rem]">
                <h2 class="font-bold mb-3">Chat with customer</h2>
                <div id="chat-messages" class="flex-1 overflow-y-auto space-y-2 mb-3 pr-1">
                    @foreach ($order->messages as $message)
                        <div class="flex {{ $message->sender_type === 'rider' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[80%] rounded-lg px-3 py-2 text-sm {{ $message->sender_type === 'rider' ? 'bg-rose-950 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }}">
                                {{ $message->body }}
                            </div>
                        </div>
                    @endforeach
                </div>
                <form id="chat-form" action="{{ route('rider.orders.message', $order) }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <input type="text" name="body" placeholder="Type a message…" required
                        class="flex-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-full px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                    <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition">Send</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var messagesEl = document.getElementById('chat-messages');
            messagesEl.scrollTop = messagesEl.scrollHeight;

            function renderMessages(messages) {
                messagesEl.innerHTML = '';
                messages.forEach(function (m) {
                    var row = document.createElement('div');
                    row.className = 'flex ' + (m.sender_type === 'rider' ? 'justify-end' : 'justify-start');
                    var bubble = document.createElement('div');
                    bubble.className = 'max-w-[80%] rounded-lg px-3 py-2 text-sm ' + (m.sender_type === 'rider' ? 'bg-rose-950 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200');
                    bubble.textContent = m.body;
                    row.appendChild(bubble);
                    messagesEl.appendChild(row);
                });
                messagesEl.scrollTop = messagesEl.scrollHeight;
            }

            setInterval(function () {
                fetch('{{ route('track.messages', $order->tracking_code) }}')
                    .then(function (r) { return r.json(); })
                    .then(function (data) { renderMessages(data.messages); });
            }, 5000);

            // Keep broadcasting live location while this delivery is open.
            if (navigator.geolocation) {
                setInterval(function () {
                    navigator.geolocation.getCurrentPosition(function (position) {
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
                        });
                    });
                }, 15000);
            }
        })();
    </script>
@endsection
