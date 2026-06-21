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
                            <div class="py-3">
                                <form action="{{ route('admin.menu-items.update', $item) }}" method="POST" class="flex items-start gap-3 flex-wrap">
                                    @csrf
                                    @method('PUT')
                                    <div class="w-10 h-10 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center overflow-hidden">
                                        @if ($item->image)
                                            <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                        @else
                                            🍲
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-[12rem] space-y-2">
                                        <input type="text" name="name" value="{{ $item->name }}" required
                                            class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                                        <textarea name="description" rows="2" placeholder="Description"
                                            class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ $item->description }}</textarea>
                                    </div>
                                    <div class="w-24 shrink-0">
                                        <label class="text-[11px] text-gray-400 dark:text-gray-500">Price (Tk)</label>
                                        <input type="number" name="price" value="{{ $item->price }}" step="0.01" min="0" required
                                            class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0 pt-0.5">
                                        <button type="submit" class="text-xs font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3 py-1.5 rounded-full transition">Save</button>
                                    </div>
                                </form>
                                <form action="{{ route('admin.menu-items.delete', $item) }}" method="POST" onsubmit="return confirm('Delete \'{{ $item->name }}\'? This cannot be undone.')" class="mt-1.5 ml-12">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Delete item</button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    @endif
@endsection
