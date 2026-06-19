@extends('layouts.dashboard', ['portal' => 'Admin'])

@section('title', 'Admin Dashboard - FoodHub')

@section('header-actions')
    <form action="{{ route('admin.logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Logout</button>
    </form>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-6">Admin Dashboard</h1>

    <div class="grid sm:grid-cols-2 gap-4 mb-8">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 shrink-0 rounded-full bg-pink-100 text-pink-600 flex items-center justify-center text-xl">🏪</div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total restaurants</p>
                <p class="text-2xl font-bold">{{ $restaurantCount }}</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 shrink-0 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-xl">👥</div>
            <div>
                <p class="text-xs text-gray-400 uppercase tracking-wide">Total customers</p>
                <p class="text-2xl font-bold">{{ $userCount }}</p>
            </div>
        </div>
    </div>

    <h2 class="text-lg font-bold mb-3">Recently joined restaurants</h2>
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm divide-y divide-gray-100">
        @forelse ($restaurants as $restaurant)
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold truncate">{{ $restaurant->name }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ $restaurant->cuisine }} &middot; {{ $restaurant->email }}</p>
                </div>
                <span class="shrink-0 text-xs font-semibold {{ $restaurant->is_open ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2.5 py-1 rounded-full">
                    {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                </span>
            </div>
        @empty
            <p class="p-4 text-gray-500 text-sm">No restaurants yet.</p>
        @endforelse
    </div>
@endsection
