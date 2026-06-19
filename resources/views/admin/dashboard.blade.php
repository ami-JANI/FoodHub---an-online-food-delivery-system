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
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 shrink-0 rounded-full bg-rose-50 dark:bg-rose-900/30 text-rose-800 dark:text-rose-400 flex items-center justify-center text-xl">🏪</div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Total restaurants</p>
                <p class="text-2xl font-bold">{{ $restaurantCount }}</p>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 flex items-center gap-4">
            <div class="w-12 h-12 shrink-0 rounded-full bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 flex items-center justify-center text-xl">👥</div>
            <div>
                <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Total customers</p>
                <p class="text-2xl font-bold">{{ $userCount }}</p>
            </div>
        </div>
    </div>

    @if ($pendingRestaurants->isNotEmpty())
        <h2 class="text-lg font-bold mb-3">Pending restaurant approvals</h2>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800 mb-8">
            @foreach ($pendingRestaurants as $restaurant)
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ $restaurant->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $restaurant->cuisine }} &middot; {{ $restaurant->owner_name }} &middot; {{ $restaurant->email }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">{{ $restaurant->address_line }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('admin.restaurants.approve', $restaurant) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full transition">Approve</button>
                        </form>
                        <form action="{{ route('admin.restaurants.reject', $restaurant) }}" method="POST" onsubmit="return confirm('Reject and remove this restaurant registration?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400 px-3 py-1.5 rounded-full transition">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($pendingMenuItems->isNotEmpty())
        <h2 class="text-lg font-bold mb-3">Pending menu items</h2>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800 mb-8">
            @foreach ($pendingMenuItems as $item)
                <div class="p-4 flex items-center gap-3">
                    <div class="w-10 h-10 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center overflow-hidden">
                        @if ($item->image)
                            <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            🍲
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold truncate">{{ $item->name }} <span class="text-gray-400 dark:text-gray-500 font-normal">— Tk {{ number_format($item->price, 0) }}</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $item->restaurant->name }} &middot; {{ $item->category->name }}</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('admin.menu-items.approve', $item) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full transition">Approve</button>
                        </form>
                        <form action="{{ route('admin.menu-items.reject', $item) }}" method="POST" onsubmit="return confirm('Reject and remove this menu item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400 px-3 py-1.5 rounded-full transition">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if ($pendingProfileUpdates->isNotEmpty())
        <h2 class="text-lg font-bold mb-3">Pending profile changes</h2>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800 mb-8">
            @foreach ($pendingProfileUpdates as $request)
                <div class="p-4 flex items-center gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="font-semibold truncate">{{ $request->restaurant->name }} <span class="text-gray-400 dark:text-gray-500 font-normal">requested changes</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">New name: {{ $request->name }} &middot; New cuisine: {{ $request->cuisine ?: '—' }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 truncate">New address: {{ $request->address_line }}</p>
                        @if ($request->description)
                            <p class="text-xs text-gray-400 dark:text-gray-500 truncate">New description: {{ $request->description }}</p>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form action="{{ route('admin.profile-updates.approve', $request) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-full transition">Approve</button>
                        </form>
                        <form action="{{ route('admin.profile-updates.reject', $request) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs font-semibold bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400 px-3 py-1.5 rounded-full transition">Reject</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="text-lg font-bold mb-3">Recently joined restaurants</h2>
    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800">
        @forelse ($restaurants as $restaurant)
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold truncate">{{ $restaurant->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $restaurant->cuisine }} &middot; {{ $restaurant->email }}</p>
                </div>
                <span class="shrink-0 text-xs font-semibold {{ $restaurant->is_open ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }} px-2.5 py-1 rounded-full">
                    {{ $restaurant->is_open ? 'Open' : 'Closed' }}
                </span>
            </div>
        @empty
            <p class="p-4 text-gray-500 dark:text-gray-400 text-sm">No restaurants yet.</p>
        @endforelse
    </div>
@endsection
