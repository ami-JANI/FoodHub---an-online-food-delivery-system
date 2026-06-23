@extends('layouts.app')

@section('title', 'Order #' . $order->tracking_code . ' - FoodHub')

@section('content')
    <style>
        @keyframes track-bob { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-6px); } }
        @keyframes track-pop { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.25); } }
        @keyframes track-sizzle { 0%, 100% { transform: rotate(-8deg); } 50% { transform: rotate(8deg); } }
        @keyframes track-drive { 0% { transform: translateX(-12px); } 50% { transform: translateX(12px); } 100% { transform: translateX(-12px); } }
        @keyframes track-celebrate { 0%, 100% { transform: rotate(0) scale(1); } 25% { transform: rotate(-12deg) scale(1.2); } 75% { transform: rotate(12deg) scale(1.2); } }
    </style>

    <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; Back home</a>

    <div class="mt-3 mb-6 flex items-center justify-between gap-3 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold">Order #{{ $order->tracking_code }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ $order->restaurant->name }}</p>
        </div>
        <span class="text-sm font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-3 py-1.5 rounded-full" id="status-badge">
            {{ $order->statusLabel() }}
        </span>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <div class="flex items-center justify-between gap-3 mb-5">
                    <h2 class="font-bold">Order status</h2>
                    @if ($order->rider && $order->status !== \App\Models\Order::DELIVERED && ! $order->isCancelled())
                        <a href="#rider-map-section" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-1.5 rounded-full transition inline-flex items-center gap-1.5 shrink-0">
                            🛵 Track rider
                        </a>
                    @endif
                </div>

                @if ($order->isCancelled())
                    <div class="flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
                        <span class="text-lg shrink-0">🚫</span>
                        <p>This order was cancelled. No further updates will appear.</p>
                    </div>
                @else
                    @php
                        $stepAnim = [
                            \App\Models\Order::PLACED            => ['icon' => '🧾', 'anim' => 'track-bob'],
                            \App\Models\Order::RESTAURANT_ACCEPTED => ['icon' => '👍', 'anim' => 'track-pop'],
                            \App\Models\Order::PREPARING         => ['icon' => '🍳', 'anim' => 'track-sizzle'],
                            \App\Models\Order::RIDER_ASSIGNED    => ['icon' => '🛵', 'anim' => 'track-bob'],
                            \App\Models\Order::RIDER_ARRIVED     => ['icon' => '🏪', 'anim' => 'track-pop'],
                            \App\Models\Order::PICKED_UP         => ['icon' => '📦', 'anim' => 'track-pop'],
                            \App\Models\Order::ON_THE_WAY        => ['icon' => '🛵', 'anim' => 'track-drive'],
                            \App\Models\Order::DELIVERED         => ['icon' => '🎉', 'anim' => 'track-celebrate'],
                        ];
                        $current = $stepAnim[$order->status] ?? ['icon' => '🍽️', 'anim' => 'track-bob'];
                    @endphp

                    <div class="flex items-center gap-4 mb-6 bg-rose-50 dark:bg-rose-900/15 rounded-xl px-4 py-4 overflow-hidden">
                        <span class="text-4xl shrink-0 inline-block" style="animation: {{ $current['anim'] }} 1.6s ease-in-out infinite;">{{ $current['icon'] }}</span>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $order->statusLabel() }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $order->status === \App\Models\Order::DELIVERED ? 'Enjoy your meal!' : 'Hang tight — we\'ll keep this updated live.' }}
                            </p>
                        </div>
                    </div>

                    {{-- Segmented horizontal progress line --}}
                    <div class="flex items-stretch gap-1.5 mb-4">
                        @foreach ($order->statusTimeline() as $step)
                            <div class="flex-1 h-2 rounded-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                                <div class="h-full rounded-full transition-all duration-500
                                    {{ $step['complete'] ? 'bg-rose-950 dark:bg-rose-700 w-full' : 'w-0' }}
                                    {{ $step['current'] ? 'animate-pulse' : '' }}"></div>
                            </div>
                        @endforeach
                    </div>

                    <ol class="grid grid-cols-2 sm:grid-cols-4 gap-x-3 gap-y-2">
                        @foreach ($order->statusTimeline() as $step)
                            <li class="flex items-center gap-2 text-xs">
                                <span class="shrink-0 w-4 h-4 rounded-full flex items-center justify-center text-[9px] font-bold
                                    {{ $step['complete'] ? 'bg-rose-950 dark:bg-rose-700 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-500' }}">
                                    {{ $step['complete'] ? '✓' : '' }}
                                </span>
                                <span class="{{ $step['current'] ? 'font-semibold text-gray-900 dark:text-gray-100' : ($step['complete'] ? 'text-gray-600 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500') }}">
                                    {{ $step['label'] }}
                                </span>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>

            @if ($isOwner && $order->canBeCancelledByCustomer())
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex items-center justify-between gap-3 flex-wrap">
                    <p class="text-sm text-gray-600 dark:text-gray-300">Changed your mind? You can cancel until the restaurant starts preparing your meal.</p>
                    <form action="{{ route('track.cancel', $order->tracking_code) }}" method="POST" onsubmit="return confirm('Cancel this order? This cannot be undone.')">
                        @csrf
                        <button type="submit" class="text-sm font-semibold bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400 px-4 py-2 rounded-full transition shrink-0">Cancel order</button>
                    </form>
                </div>
            @elseif ($isOwner && ! $order->isCancelled() && $order->status !== \App\Models\Order::DELIVERED)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 text-sm text-gray-500 dark:text-gray-400">
                    🍳 The restaurant has started preparing your meal, so this order can no longer be cancelled.
                </div>
            @endif

            @if ($isOwner && $order->status === \App\Models\Order::DELIVERED)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex items-center justify-between gap-3 flex-wrap">
                    @if ($order->review)
                        <p class="text-sm text-gray-600 dark:text-gray-300">⭐ You rated this order {{ $order->review->rating }}/5. Thanks for your feedback!</p>
                    @else
                        <p class="text-sm text-gray-600 dark:text-gray-300">How was your order from {{ $order->restaurant->name }}?</p>
                        <a href="{{ route('reviews.create', $order->tracking_code) }}" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition shrink-0">Leave a review</a>
                    @endif
                </div>
            @endif

            @if ($order->rider && $order->status !== \App\Models\Order::DELIVERED)
                <div id="rider-map-section" class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 scroll-mt-24">
                    <h2 class="font-bold mb-1">Your rider</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">{{ $order->rider->name }} &middot; {{ $order->rider->vehicle_type }} &middot; 📞 {{ $order->rider->phone }}</p>

                    @include('partials.map-display', [
                        'mapId' => 'rider-tracking-map',
                        'lat' => $order->rider->last_latitude ?? $order->latitude,
                        'lng' => $order->rider->last_longitude ?? $order->longitude,
                        'markerLabel' => 'Rider',
                    ])
                    <p id="rider-location-status" class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                        Live location updates automatically.
                    </p>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                <h2 class="font-bold mb-3">Order details</h2>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-1">📍 {{ $order->address_line }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">📞 {{ $order->phone }}</p>
                <div class="space-y-1 border-t border-gray-100 dark:border-gray-800 pt-3">
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
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex flex-col h-[28rem] sticky top-24">
                <h2 class="font-bold mb-3">Chat with rider</h2>

                @if (! $isOwner)
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        <a href="{{ route('login') }}" class="text-rose-800 dark:text-rose-400 hover:underline">Sign in</a> as the customer who placed this order to chat with the rider.
                    </p>
                @elseif (! $order->rider)
                    <p class="text-sm text-gray-500 dark:text-gray-400">A chat will open here once a rider is assigned to your order.</p>
                @else
                    <div id="chat-messages" class="flex-1 overflow-y-auto space-y-2 mb-3 pr-1">
                        @foreach ($order->messages as $message)
                            <div class="flex {{ $message->sender_type === 'customer' ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-[80%] rounded-lg px-3 py-2 text-sm {{ $message->sender_type === 'customer' ? 'bg-rose-950 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200' }}">
                                    {{ $message->body }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <form id="chat-form" action="{{ route('track.message', $order->tracking_code) }}" method="POST" class="flex items-center gap-2">
                        @csrf
                        <input type="text" name="body" placeholder="Type a message…" required
                            class="flex-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-full px-3.5 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                        <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-4 py-2 rounded-full transition">Send</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    @if ($isOwner && $order->rider)
        <script>
            (function () {
                var messagesEl = document.getElementById('chat-messages');
                if (messagesEl) {
                    messagesEl.scrollTop = messagesEl.scrollHeight;
                }

                function renderMessages(messages) {
                    if (!messagesEl) return;
                    messagesEl.innerHTML = '';
                    messages.forEach(function (m) {
                        var row = document.createElement('div');
                        row.className = 'flex ' + (m.sender_type === 'customer' ? 'justify-end' : 'justify-start');
                        var bubble = document.createElement('div');
                        bubble.className = 'max-w-[80%] rounded-lg px-3 py-2 text-sm ' + (m.sender_type === 'customer' ? 'bg-rose-950 text-white' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200');
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
            })();
        </script>
    @endif

    @if ($order->rider && $order->status !== \App\Models\Order::DELIVERED)
        <script>
            (function () {
                setInterval(function () {
                    fetch('{{ route('track.rider-location', $order->tracking_code) }}')
                        .then(function (r) { return r.json(); })
                        .then(function (data) {
                            if (data.status_label) {
                                document.getElementById('status-badge').textContent = data.status_label;
                            }
                            if (data.rider && data.rider.latitude && window['rider-tracking-map_map']) {
                                var map = window['rider-tracking-map_map'];
                                var latlng = [data.rider.latitude, data.rider.longitude];
                                if (window['rider-tracking-map_secondaryMarker']) {
                                    window['rider-tracking-map_secondaryMarker'].setLatLng(latlng);
                                } else {
                                    window['rider-tracking-map_secondaryMarker'] = L.marker(latlng).addTo(map);
                                }
                                document.getElementById('rider-location-status').textContent =
                                    'Rider last seen ' + (data.rider.last_seen_at || 'just now') + '.';
                            }
                        });
                }, 10000);
            })();
        </script>
    @endif
@endsection
