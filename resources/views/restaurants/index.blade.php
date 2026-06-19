@extends('layouts.app')

@section('title', 'FoodHub - Order food online')

@section('content')
    <div class="bg-gradient-to-br from-pink-600 to-orange-500 rounded-2xl px-6 py-10 sm:px-10 sm:py-14 text-white mb-8 shadow-lg">
        <h1 class="text-2xl sm:text-3xl font-extrabold mb-2">Hungry? We've got you covered.</h1>
        <p class="text-pink-50 sm:text-lg mb-5">Order from {{ $restaurants->count() }} restaurants near you — delivered fast, fresh, and hot.</p>
        <div class="flex items-center gap-2 bg-white rounded-full px-4 py-2.5 max-w-md shadow">
            <span class="text-gray-400">📍</span>
            <span class="text-gray-500 text-sm">Dhaka, Bangladesh</span>
        </div>
    </div>

    <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-bold">Restaurants near you</h2>
        <span class="text-sm text-gray-500">{{ $restaurants->count() }} results</span>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @php($gradients = ['from-pink-400 to-orange-300', 'from-orange-400 to-amber-300', 'from-rose-400 to-pink-300', 'from-amber-400 to-orange-300'])
        @foreach ($restaurants as $restaurant)
            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
               class="group block bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all hover:-translate-y-0.5 overflow-hidden border border-gray-100">
                <div class="h-36 bg-gradient-to-br {{ $gradients[$loop->index % count($gradients)] }} flex items-center justify-center text-white text-4xl relative">
                    🍽️
                    @unless ($restaurant->is_open)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-sm font-semibold bg-white text-gray-900 px-3 py-1 rounded-full">Closed</span>
                        </div>
                    @endunless
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-bold text-lg group-hover:text-pink-600 transition truncate">{{ $restaurant->name }}</h3>
                        <span class="shrink-0 text-sm bg-green-50 text-green-700 px-2 py-0.5 rounded-md font-semibold">★ {{ $restaurant->rating }}</span>
                    </div>
                    <p class="text-sm text-gray-500 truncate">{{ $restaurant->cuisine }}</p>
                    <div class="flex items-center gap-3 text-xs text-gray-400 mt-3 pt-3 border-t border-gray-100">
                        <span>⏱ {{ $restaurant->delivery_time }}</span>
                        <span>🚲 Tk {{ number_format($restaurant->delivery_fee, 0) }} delivery</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
