@extends('layouts.app')

@section('title', $restaurant->name . ' - FoodHub')

@section('content')
    <a href="{{ route('home') }}" class="text-sm text-gray-500 hover:text-pink-600 transition inline-flex items-center gap-1">&larr; All restaurants</a>

    <div class="mt-4 mb-8 bg-gradient-to-br from-pink-600 to-orange-500 rounded-2xl px-6 py-8 text-white shadow-lg">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold">{{ $restaurant->name }}</h1>
                <p class="text-pink-50 mt-1">{{ $restaurant->cuisine }}</p>
            </div>
            <span class="shrink-0 text-sm font-semibold {{ $restaurant->is_open ? 'bg-white text-green-700' : 'bg-white/80 text-gray-600' }} px-3 py-1 rounded-full">
                {{ $restaurant->is_open ? 'Open now' : 'Closed' }}
            </span>
        </div>
        <div class="flex items-center gap-4 text-sm mt-4 text-pink-50">
            <span class="flex items-center gap-1">★ {{ $restaurant->rating }}</span>
            <span class="flex items-center gap-1">⏱ {{ $restaurant->delivery_time }}</span>
            <span class="flex items-center gap-1">🚲 Tk {{ number_format($restaurant->delivery_fee, 0) }} delivery</span>
        </div>
    </div>

    @if ($restaurant->categories->isNotEmpty())
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-2 sticky top-[68px] bg-gray-50/95 backdrop-blur z-[5] -mx-1 px-1">
            @foreach ($restaurant->categories as $category)
                <a href="#category-{{ $category->id }}"
                   class="shrink-0 text-sm font-medium bg-white border border-gray-200 hover:border-pink-400 hover:text-pink-600 transition px-3.5 py-1.5 rounded-full">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    @endif

    @foreach ($restaurant->categories as $category)
        <h2 id="category-{{ $category->id }}" class="text-lg font-bold mb-3 mt-8 scroll-mt-32">{{ $category->name }}</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach ($category->menuItems as $item)
                <div class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md transition p-4 flex items-center gap-4">
                    <div class="w-16 h-16 shrink-0 rounded-lg bg-gradient-to-br from-orange-200 to-pink-200 flex items-center justify-center text-2xl">
                        🍲
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold truncate">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500 line-clamp-2">{{ $item->description }}</p>
                        <p class="text-sm font-bold mt-1 text-gray-800">Tk {{ number_format($item->price, 0) }}</p>
                    </div>
                    <form action="{{ route('cart.add', $item->id) }}" method="POST" class="shrink-0">
                        @csrf
                        <button type="submit"
                            class="bg-pink-600 group-hover:bg-pink-700 text-white text-sm font-semibold px-3.5 py-2 rounded-full whitespace-nowrap transition">
                            + Add
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
