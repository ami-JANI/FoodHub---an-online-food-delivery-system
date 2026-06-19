@extends('layouts.dashboard', ['portal' => 'Partner'])

@section('title', 'Dashboard - ' . $restaurant->name)

@section('header-actions')
    <a href="{{ route('restaurant.profile.edit') }}" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Edit profile</a>
    <form action="{{ route('restaurant.logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Logout</button>
    </form>
@endsection

@section('content')
    @unless ($restaurant->is_approved)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⏳</span>
            <p>Your restaurant is pending admin approval. It won't be visible to customers until approved.</p>
        </div>
    @endunless

    @if ($pendingUpdateRequest)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">📝</span>
            <p>You have a profile change request awaiting admin review.</p>
        </div>
    @endif

    <div class="mb-6 flex items-center gap-4">
        <div class="w-14 h-14 shrink-0 rounded-xl bg-gradient-to-br from-stone-700 to-rose-950 flex items-center justify-center text-2xl text-white overflow-hidden">
            @if ($restaurant->logo)
                <img src="{{ asset('uploads/' . $restaurant->logo) }}" alt="Logo" class="w-full h-full object-cover">
            @else
                🍽️
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-bold">{{ $restaurant->name }}</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Owner: {{ $restaurant->owner_name }} &middot; {{ $restaurant->email }}</p>
        </div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Rating</p>
            <p class="text-2xl font-bold mt-1">★ {{ $restaurant->rating }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Menu categories</p>
            <p class="text-2xl font-bold mt-1">{{ $restaurant->categories->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Menu items</p>
            <p class="text-2xl font-bold mt-1">{{ $restaurant->categories->sum(fn ($c) => $c->menuItems->count()) }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-3 mt-6">
        <h2 class="text-lg font-bold">Menu</h2>
        <a href="{{ route('restaurant.menu-items.create') }}" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-1.5 rounded-full transition">+ Add menu item</a>
    </div>

    @if ($restaurant->categories->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 text-center text-gray-500 dark:text-gray-400">
            You haven't added any menu items yet.
        </div>
    @else
        @foreach ($restaurant->categories as $category)
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2 mt-5">{{ $category->name }}</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach ($category->menuItems as $item)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center gap-3">
                        <div class="w-12 h-12 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center text-xl overflow-hidden">
                            @if ($item->image)
                                <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                🍲
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $item->name }}</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Tk {{ number_format($item->price, 0) }}</p>
                            @unless ($item->is_approved)
                                <span class="text-[10px] font-bold uppercase bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded">Pending approval</span>
                            @endunless
                        </div>
                        <a href="{{ route('restaurant.menu-items.edit', $item) }}" class="text-xs font-semibold text-rose-800 dark:text-rose-400 hover:underline shrink-0">Edit</a>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
@endsection
