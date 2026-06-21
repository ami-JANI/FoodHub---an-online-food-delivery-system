@extends('layouts.app')

@section('title', 'My Account - FoodHub')

@section('content')
    <h1 class="text-2xl font-bold mb-1">My Account</h1>
    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $user->name }} &middot; {{ $user->email }}</p>

    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold">Saved addresses</h2>
        <a href="{{ route('addresses.create') }}" class="text-sm font-semibold text-rose-800 dark:text-rose-400 hover:underline">+ Add address</a>
    </div>

    @if ($addresses->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
            You haven't saved any delivery addresses yet.
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-3 mb-8">
            @foreach ($addresses as $address)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <p class="font-semibold text-sm flex items-center gap-2">
                            {{ $address->label }}
                            @if ($address->is_default)
                                <span class="text-[10px] font-bold uppercase bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded">Default</span>
                            @endif
                        </p>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $address->address_line }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-3">📞 {{ $address->phone }}</p>

                    <div class="flex items-center gap-3 text-xs font-semibold">
                        <a href="{{ route('addresses.edit', $address) }}" class="text-rose-800 dark:text-rose-400 hover:underline">Edit</a>
                        @unless ($address->is_default)
                            <form action="{{ route('addresses.default', $address) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="text-gray-500 dark:text-gray-400 hover:underline">Set default</button>
                            </form>
                        @endunless
                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Remove this address?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Remove</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="text-lg font-bold mb-3">My favorites</h2>
    @if ($favorites->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
            You haven't favorited any restaurants yet.
        </div>
    @else
        <div class="grid sm:grid-cols-2 gap-3 mb-8">
            @foreach ($favorites as $favorite)
                @continue (! $favorite->restaurant)
                <a href="{{ route('restaurants.show', $favorite->restaurant->slug) }}"
                   class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center gap-3 hover:border-rose-400 transition">
                    <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center overflow-hidden">
                        @if ($favorite->restaurant->logo)
                            <img src="{{ asset('uploads/' . $favorite->restaurant->logo) }}" alt="{{ $favorite->restaurant->name }}" class="w-full h-full object-cover">
                        @else
                            🍽️
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ $favorite->restaurant->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $favorite->restaurant->cuisine }}</p>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <h2 class="text-lg font-bold mb-3">Recent orders</h2>
    @if ($orders->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400">
            You haven't placed any orders yet.
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($orders as $order)
                <div class="p-4 flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="font-semibold truncate">{{ $order->restaurant->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $order->address_line }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <p class="font-bold shrink-0">Tk {{ number_format($order->total, 0) }}</p>
                </div>
            @endforeach
        </div>
    @endif
@endsection
