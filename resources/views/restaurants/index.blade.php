@extends('layouts.app')

@section('title', 'FoodHub - Order food online')

@section('content')
    <h1 class="text-2xl font-bold mb-1">Restaurants near you</h1>
    <p class="text-gray-500 mb-6">Order food from your favorite restaurants.</p>

    <div class="grid sm:grid-cols-2 gap-4">
        @foreach ($restaurants as $restaurant)
            <a href="{{ route('restaurants.show', $restaurant->slug) }}"
               class="block bg-white rounded-xl shadow hover:shadow-md transition overflow-hidden border border-gray-100">
                <div class="h-32 bg-gradient-to-br from-pink-400 to-orange-300 flex items-center justify-center text-white text-3xl">
                    🍽️
                </div>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <h2 class="font-semibold text-lg">{{ $restaurant->name }}</h2>
                        <span class="text-sm bg-green-100 text-green-700 px-2 py-0.5 rounded">★ {{ $restaurant->rating }}</span>
                    </div>
                    <p class="text-sm text-gray-500">{{ $restaurant->cuisine }}</p>
                    <div class="flex items-center gap-3 text-xs text-gray-400 mt-2">
                        <span>⏱ {{ $restaurant->delivery_time }}</span>
                        <span>🚲 Tk {{ number_format($restaurant->delivery_fee, 0) }} delivery</span>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
@endsection
