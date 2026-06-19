@extends('layouts.dashboard', ['portal' => 'Partner'])

@section('title', 'Dashboard - ' . $restaurant->name)

@section('header-actions')
    <form action="{{ route('restaurant.logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Logout</button>
    </form>
@endsection

@section('content')
    <div class="mb-6 flex items-center gap-4">
        <div class="w-14 h-14 shrink-0 rounded-xl bg-gradient-to-br from-pink-500 to-orange-400 flex items-center justify-center text-2xl text-white">
            🍽️
        </div>
        <div>
            <h1 class="text-2xl font-bold">{{ $restaurant->name }}</h1>
            <p class="text-gray-500 text-sm">Owner: {{ $restaurant->owner_name }} &middot; {{ $restaurant->email }}</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Rating</p>
            <p class="text-2xl font-bold mt-1">★ {{ $restaurant->rating }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Menu categories</p>
            <p class="text-2xl font-bold mt-1">{{ $restaurant->categories->count() }}</p>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4">
            <p class="text-xs text-gray-400 uppercase tracking-wide">Menu items</p>
            <p class="text-2xl font-bold mt-1">{{ $restaurant->categories->sum(fn ($c) => $c->menuItems->count()) }}</p>
        </div>
    </div>

    @if ($restaurant->categories->isEmpty())
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-8 text-center text-gray-500">
            You haven't added any menu items yet.
        </div>
    @else
        @foreach ($restaurant->categories as $category)
            <h2 class="text-lg font-bold mb-3 mt-6">{{ $category->name }}</h2>
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach ($category->menuItems as $item)
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center justify-between gap-3">
                        <h3 class="font-medium">{{ $item->name }}</h3>
                        <p class="text-sm font-bold text-gray-800 shrink-0">Tk {{ number_format($item->price, 0) }}</p>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
@endsection
