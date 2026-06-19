@extends('layouts.app')

@section('title', $restaurant->name . ' - FoodHub')

@section('content')
    <a href="{{ route('home') }}" class="text-sm text-pink-600 hover:underline">&larr; All restaurants</a>

    <div class="mt-3 mb-6">
        <h1 class="text-2xl font-bold">{{ $restaurant->name }}</h1>
        <p class="text-gray-500">{{ $restaurant->cuisine }}</p>
        <div class="flex items-center gap-3 text-xs text-gray-400 mt-1">
            <span>★ {{ $restaurant->rating }}</span>
            <span>⏱ {{ $restaurant->delivery_time }}</span>
            <span>🚲 Tk {{ number_format($restaurant->delivery_fee, 0) }} delivery</span>
        </div>
    </div>

    @foreach ($restaurant->categories as $category)
        <h2 class="text-lg font-semibold mb-3 mt-6">{{ $category->name }}</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach ($category->menuItems as $item)
                <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-medium">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500">{{ $item->description }}</p>
                        <p class="text-sm font-semibold mt-1">Tk {{ number_format($item->price, 0) }}</p>
                    </div>
                    <form action="{{ route('cart.add', $item->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                            class="bg-pink-600 hover:bg-pink-700 text-white text-sm font-medium px-3 py-1.5 rounded-full whitespace-nowrap">
                            + Add
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
