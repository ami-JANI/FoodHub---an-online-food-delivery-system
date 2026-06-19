@extends('layouts.dashboard', ['portal' => 'Partner'])

@section('title', ($menuItem ? 'Edit Menu Item' : 'Add Menu Item') . ' - FoodHub')

@section('header-actions')
    <a href="{{ route('restaurant.dashboard') }}" class="text-sm font-medium hover:underline">&larr; Dashboard</a>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-6">{{ $menuItem ? 'Edit menu item' : 'Add a menu item' }}</h1>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 max-w-xl">
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-2 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $menuItem ? route('restaurant.menu-items.update', $menuItem) : route('restaurant.menu-items.store') }}"
              method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @if ($menuItem)
                @method('PUT')
            @endif

            <div>
                <label class="text-sm font-medium">Item name</label>
                <input type="text" name="name" value="{{ old('name', $menuItem->name ?? '') }}" required
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Description</label>
                <textarea name="description" rows="2"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ old('description', $menuItem->description ?? '') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Price (Tk)</label>
                <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $menuItem->price ?? '') }}" required
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Category</label>
                <select name="category_id"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                    <option value="">— Select existing category —</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ ($menuItem?->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Or create a new category:</p>
                <input type="text" name="new_category" value="{{ old('new_category') }}" placeholder="e.g. Drinks"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Photo</label>
                @if ($menuItem?->image)
                    <img src="{{ asset('uploads/' . $menuItem->image) }}" alt="{{ $menuItem->name }}" class="w-24 h-24 object-cover rounded-lg mt-1 mb-2">
                @endif
                <input type="file" name="image" accept="image/*"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" name="is_available" value="1" {{ old('is_available', $menuItem->is_available ?? true) ? 'checked' : '' }}>
                Available for order
            </label>

            <p class="text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2">
                ⚠️ This item will be hidden from customers until an admin reviews and approves it.
            </p>

            <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
                {{ $menuItem ? 'Save changes' : 'Add item' }}
            </button>
        </form>
    </div>
@endsection
