@extends('layouts.app')

@section('title', ($address ? 'Edit Address' : 'Add Address') . ' - FoodHub')

@section('content')
    <a href="{{ route('account.show') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; My account</a>

    <h1 class="text-2xl font-bold mt-3 mb-6">{{ $address ? 'Edit address' : 'Add a new address' }}</h1>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 max-w-2xl">
        @if ($errors->any())
            <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-2 text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $address ? route('addresses.update', $address) : route('addresses.store') }}" method="POST" class="space-y-4">
            @csrf
            @if ($address)
                @method('PUT')
            @endif

            <div>
                <label class="text-sm font-medium">Label</label>
                <input type="text" name="label" value="{{ old('label', $address->label ?? 'Home') }}" required
                    placeholder="e.g. Home, Work"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            <div>
                <label class="text-sm font-medium">Address</label>
                <textarea name="address_line" rows="2" required placeholder="House, road, area, city..."
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ old('address_line', $address->address_line ?? '') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-medium">Phone number</label>
                <input type="text" name="phone" value="{{ old('phone', $address->phone ?? '') }}" required
                    placeholder="01XXXXXXXXX"
                    class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>

            @include('partials.map-picker', [
                'mapId' => 'address-map',
                'initialLat' => $address->latitude ?? null,
                'initialLng' => $address->longitude ?? null,
            ])

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" name="make_default" value="1" {{ ($address->is_default ?? false) ? 'checked' : '' }}>
                Set as default address
            </label>

            <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
                {{ $address ? 'Save changes' : 'Save address' }}
            </button>
        </form>
    </div>
@endsection
