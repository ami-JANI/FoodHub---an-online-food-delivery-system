@extends('layouts.app')

@section('title', 'Your Cart - FoodHub')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Your Cart</h1>

    @error('cart')
        <div class="mb-4 flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⚠️</span>
            <p>{{ $message }}</p>
        </div>
    @enderror

    @if (empty($items))
        <div class="text-center bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm py-16 px-6">
            <div class="text-5xl mb-4">🛒</div>
            <h2 class="text-lg font-semibold mb-1">Your cart is empty</h2>
            <p class="text-gray-500 dark:text-gray-400 mb-5">Looks like you haven't added anything yet.</p>
            <a href="{{ route('home') }}" class="bg-rose-950 hover:bg-rose-900 text-white font-semibold px-5 py-2.5 rounded-full inline-block transition">
                Browse restaurants
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                @if ($restaurant)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Order from <span class="font-semibold text-gray-800 dark:text-gray-200">{{ $restaurant->name }}</span></p>
                @endif

                <div class="space-y-3">
                    @foreach ($items as $row)
                        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center text-xl">
                                🍲
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold truncate">{{ $row['menuItem']->name }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tk {{ number_format($row['menuItem']->price, 0) }} each</p>
                            </div>

                            <form action="{{ route('cart.update', $row['menuItem']->id) }}" method="POST" class="flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="qty" value="{{ $row['qty'] }}" min="0" max="20"
                                    class="w-14 border border-gray-200 dark:border-gray-700 bg-transparent rounded-lg px-2 py-1.5 text-sm text-center">
                                <button type="submit" class="text-xs font-semibold text-rose-800 dark:text-rose-400 hover:underline whitespace-nowrap">Update</button>
                            </form>

                            <p class="font-bold w-20 text-right shrink-0">Tk {{ number_format($row['subtotal'], 0) }}</p>

                            <form action="{{ route('cart.remove', $row['menuItem']->id) }}" method="POST" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition w-7 h-7 flex items-center justify-center rounded-full hover:bg-red-50 dark:hover:bg-red-900/30">✕</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 sticky top-24">
                    <h2 class="font-bold mb-3">Order Summary</h2>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                        <span>Subtotal</span>
                        <span>Tk {{ number_format($total, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mt-1.5">
                        <span>Delivery fee</span>
                        <span>Tk {{ number_format($deliveryFee, 0) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <span>Total</span>
                        <span>Tk {{ number_format($total + $deliveryFee, 0) }}</span>
                    </div>

                    @if ($restaurant && $total < $restaurant->minimum_order)
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-3">Add Tk {{ number_format($restaurant->minimum_order - $total, 0) }} more to reach the Tk {{ number_format($restaurant->minimum_order, 0) }} minimum order.</p>
                    @endif

                    <a href="{{ route('checkout.index') }}" class="block mt-5 w-full text-center bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
