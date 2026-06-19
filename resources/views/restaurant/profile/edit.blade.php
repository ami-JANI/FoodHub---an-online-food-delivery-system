@extends('layouts.dashboard', ['portal' => 'Partner'])

@section('title', 'Edit Profile - FoodHub')

@section('header-actions')
    <a href="{{ route('restaurant.dashboard') }}" class="text-sm font-medium hover:underline">&larr; Dashboard</a>
@endsection

@section('content')
    <h1 class="text-2xl font-bold mb-6">Edit restaurant profile</h1>

    @if ($pendingUpdateRequest)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm max-w-xl">
            <span class="text-lg shrink-0">⏳</span>
            <p>You already have a pending profile change request submitted on {{ $pendingUpdateRequest->created_at->format('d M Y') }}. Submitting another will replace it.</p>
        </div>
    @endif

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

        <form action="{{ route('restaurant.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="text-sm font-medium">Restaurant name</label>
                <input type="text" name="name" value="{{ old('name', $restaurant->name) }}" required
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Cuisine type</label>
                <input type="text" name="cuisine" value="{{ old('cuisine', $restaurant->cuisine) }}"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Description</label>
                <textarea name="description" rows="3"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ old('description', $restaurant->description) }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Logo</label>
                @if ($restaurant->logo)
                    <img src="{{ asset('uploads/' . $restaurant->logo) }}" alt="Logo" class="w-20 h-20 object-cover rounded-lg mt-1 mb-2">
                @endif
                <input type="file" name="logo" accept="image/*"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Cover photo</label>
                @if ($restaurant->cover_image)
                    <img src="{{ asset('uploads/' . $restaurant->cover_image) }}" alt="Cover" class="w-full h-28 object-cover rounded-lg mt-1 mb-2">
                @endif
                <input type="file" name="cover_image" accept="image/*"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Address</label>
                <textarea name="address_line" rows="2" required
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ old('address_line', $restaurant->address_line) }}</textarea>
            </div>

            @include('partials.map-picker', [
                'mapId' => 'restaurant-profile-map',
                'initialLat' => $restaurant->latitude,
                'initialLng' => $restaurant->longitude,
            ])

            <p class="text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2">
                ⚠️ These changes will be reviewed by an admin before they go live. Your current public profile stays unchanged until approved.
            </p>

            <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
                Submit for approval
            </button>
        </form>
    </div>
@endsection
