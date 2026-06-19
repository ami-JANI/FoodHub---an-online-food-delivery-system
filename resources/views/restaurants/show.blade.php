@extends('layouts.app')

@section('title', $restaurant->name . ' - FoodHub')

@section('content')
    <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; All restaurants</a>

    @if ($cartConflict)
        <div class="mt-4 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⚠️</span>
            <p>
                You already have items from <span class="font-semibold">{{ $cartConflict->name }}</span> in your cart.
                Adding something from <span class="font-semibold">{{ $restaurant->name }}</span> will clear those items and start a new order.
            </p>
        </div>
    @endif

    <div class="mt-4 mb-8 bg-gradient-to-br from-stone-900 to-rose-950 rounded-2xl px-6 py-8 text-white shadow-lg">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold">{{ $restaurant->name }}</h1>
                <p class="text-stone-300 mt-1">{{ $restaurant->cuisine }}</p>
            </div>
            <span class="shrink-0 text-sm font-semibold {{ $restaurant->is_open ? 'bg-white text-green-700' : 'bg-white/80 text-gray-600' }} px-3 py-1 rounded-full">
                {{ $restaurant->is_open ? 'Open now' : 'Closed' }}
            </span>
        </div>
        <div class="flex items-center gap-4 text-sm mt-4 text-stone-300">
            <span class="flex items-center gap-1">★ {{ $restaurant->rating }}</span>
            <span class="flex items-center gap-1">⏱ {{ $restaurant->delivery_time }}</span>
            <span class="flex items-center gap-1">🚲 Tk {{ number_format($restaurant->computed_delivery_fee, 0) }} delivery</span>
            @if ($restaurant->distance_km !== null)
                <span class="flex items-center gap-1">📍 {{ number_format($restaurant->distance_km, 1) }} km away</span>
            @endif
        </div>
    </div>

    @if ($restaurant->categories->isNotEmpty())
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-2 sticky top-[68px] bg-gray-50/95 dark:bg-gray-950/95 backdrop-blur z-[5] -mx-1 px-1">
            @foreach ($restaurant->categories as $category)
                <a href="#category-{{ $category->id }}"
                   class="shrink-0 text-sm font-medium bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-rose-400 hover:text-rose-800 dark:hover:text-rose-400 transition px-3.5 py-1.5 rounded-full">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    @endif

    @foreach ($restaurant->categories as $category)
        <h2 id="category-{{ $category->id }}" class="text-lg font-bold mb-3 mt-8 scroll-mt-32">{{ $category->name }}</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach ($category->menuItems as $item)
                <div class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition p-4 flex items-center gap-4">
                    <div class="w-16 h-16 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center text-2xl overflow-hidden">
                        @if ($item->image)
                            <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            🍲
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold truncate">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $item->description }}</p>
                        <p class="text-sm font-bold mt-1 text-gray-800 dark:text-gray-200">Tk {{ number_format($item->price, 0) }}</p>
                    </div>
                    <form action="{{ route('cart.add', $item->id) }}" method="POST"
                        @class(['shrink-0', 'cart-conflict-form' => $cartConflict])
                        @if ($cartConflict)
                            data-conflict-restaurant="{{ $cartConflict->name }}"
                            data-target-restaurant="{{ $restaurant->name }}"
                        @endif
                    >
                        @csrf
                        <button type="submit"
                            class="bg-rose-950 group-hover:bg-rose-900 text-white text-sm font-semibold px-3.5 py-2 rounded-full whitespace-nowrap transition">
                            + Add
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endforeach

    @if ($cartConflict)
        <script>
            document.querySelectorAll('.cart-conflict-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    var from = form.dataset.conflictRestaurant;
                    var to = form.dataset.targetRestaurant;
                    var message = 'Your cart has items from ' + from + '. Adding this will clear your cart and start a new order from ' + to + '. Continue?';

                    if (!confirm(message)) {
                        event.preventDefault();
                    }
                });
            });
        </script>
    @endif
@endsection
