@extends('layouts.dashboard', ['portal' => 'Admin'])

@section('title', 'Manage menu - ' . $restaurant->name)

@section('header-actions')
    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Dashboard</a>
@endsection

@section('content')
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; Back to dashboard</a>

    <h1 class="text-2xl font-bold mt-3 mb-1">Manage menu</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $restaurant->name }} &middot; {{ $restaurant->email }}</p>

    @if ($restaurant->categories->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 text-center text-gray-500 dark:text-gray-400">
            This restaurant has no categories or menu items yet.
        </div>
    @else
        @foreach ($restaurant->categories as $category)
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-5">
                <h2 class="text-sm font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-3">{{ $category->name }}</h2>

                @if ($category->menuItems->isEmpty())
                    <p class="text-sm text-gray-400 dark:text-gray-500">No items in this category.</p>
                @else
                    <div class="divide-y divide-gray-100 dark:divide-gray-800">
                        @foreach ($category->menuItems as $item)
                            <div class="py-3 flex items-center gap-3">
                                <div class="w-10 h-10 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center overflow-hidden">
                                    @if ($item->image)
                                        <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                    @else
                                        🍲
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium truncate">{{ $item->name }} <span class="text-gray-400 dark:text-gray-500 font-normal">— Tk {{ number_format($item->price, 0) }}</span></p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $item->description ?: 'No description' }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
@endsection
