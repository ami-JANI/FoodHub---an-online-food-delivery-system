@extends('layouts.app')

@section('title', 'FoodHub - Order food online')

@section('filterbar')
    <div class="flex items-center gap-2 flex-nowrap">
        <div class="relative group shrink-0">
            <button type="button" class="flex items-center gap-1.5 text-sm font-medium border border-gray-200 dark:border-gray-700 rounded-full px-3.5 py-1.5 hover:border-rose-400 transition whitespace-nowrap">
                ⚙️ Sort By <span class="text-xs">▾</span>
            </button>
            <div class="absolute left-0 top-full hidden group-hover:block bg-white dark:bg-gray-900 rounded-lg shadow-lg border border-gray-100 dark:border-gray-800 py-2 w-40 z-30">
                <a href="{{ route('home', array_merge(request()->except('sort'), ['sort' => 'rating'])) }}" class="block px-4 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 {{ $sort === 'rating' ? 'text-rose-800 dark:text-rose-400 font-semibold' : '' }}">★ Rating</a>
                <a href="{{ route('home', array_merge(request()->except('sort'), ['sort' => 'distance'])) }}" class="block px-4 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 {{ $sort === 'distance' ? 'text-rose-800 dark:text-rose-400 font-semibold' : '' }}">📍 Distance</a>
                <a href="{{ route('home', array_merge(request()->except('sort'), ['sort' => 'delivery_fee'])) }}" class="block px-4 py-1.5 text-sm hover:bg-gray-50 dark:hover:bg-gray-800 {{ $sort === 'delivery_fee' ? 'text-rose-800 dark:text-rose-400 font-semibold' : '' }}">🚲 Delivery fee</a>
            </div>
        </div>

        @foreach ($cuisines as $c)
            <a href="{{ route('home', $activeCuisine === $c ? request()->except('cuisine') : array_merge(request()->except('cuisine'), ['cuisine' => $c])) }}"
               class="shrink-0 text-sm font-medium rounded-full px-3.5 py-1.5 transition whitespace-nowrap {{ $activeCuisine === $c ? 'bg-rose-950 text-white' : 'border border-gray-200 dark:border-gray-700 hover:border-rose-400' }}">
                {{ $c }}
            </a>
        @endforeach

        <span class="ml-auto pl-4 text-sm text-gray-400 dark:text-gray-500 shrink-0 whitespace-nowrap">
            {{ $hasLocation ? 'Showing restaurants within 5km' : 'Showing all restaurants' }}
        </span>
    </div>
@endsection

@section('content')
    <div class="bg-gradient-to-br from-stone-900 to-rose-950 rounded-2xl px-6 py-10 sm:px-10 sm:py-14 text-white mb-8 shadow-lg">
        <h1 class="text-2xl sm:text-3xl font-extrabold mb-2">Hungry? We've got you covered.</h1>
        <p class="text-stone-300 sm:text-lg">Order from {{ $restaurants->count() }} restaurants near you — delivered fast, fresh, and hot.</p>
    </div>

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

    @if ($restaurants->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-10 text-center text-gray-500 dark:text-gray-400">
            No restaurants match your filters.
            <a href="{{ route('home') }}" class="text-rose-800 dark:text-rose-400 hover:underline">Clear filters</a>
        </div>
    @else
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
    @endif
@endsection
