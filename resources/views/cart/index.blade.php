@extends('layouts.app')

@section('title', 'Your Cart - FoodHub')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Your Cart</h1>

    @if (empty($items))
        <div class="text-center bg-white rounded-2xl border border-gray-100 shadow-sm py-16 px-6">
            <div class="text-5xl mb-4">🛒</div>
            <h2 class="text-lg font-semibold mb-1">Your cart is empty</h2>
            <p class="text-gray-500 mb-5">Looks like you haven't added anything yet.</p>
            <a href="{{ route('home') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold px-5 py-2.5 rounded-full inline-block transition">
                Browse restaurants
            </a>
        </div>
    @else
        <div class="grid lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2">
                @if ($restaurant)
                    <p class="text-sm text-gray-500 mb-4">Order from <span class="font-semibold text-gray-800">{{ $restaurant->name }}</span></p>
                @endif

                <div class="space-y-3">
                    @foreach ($items as $row)
                        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-4">
                            <div class="w-12 h-12 shrink-0 rounded-lg bg-gradient-to-br from-orange-200 to-pink-200 flex items-center justify-center text-xl">
                                🍲
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold truncate">{{ $row['menuItem']->name }}</h3>
                                <p class="text-sm text-gray-500">Tk {{ number_format($row['menuItem']->price, 0) }} each</p>
                            </div>

                            <form action="{{ route('cart.update', $row['menuItem']->id) }}" method="POST" class="flex items-center gap-1.5">
                                @csrf
                                @method('PATCH')
                                <input type="number" name="qty" value="{{ $row['qty'] }}" min="0" max="20"
                                    class="w-14 border border-gray-200 rounded-lg px-2 py-1.5 text-sm text-center">
                                <button type="submit" class="text-xs font-semibold text-pink-600 hover:underline whitespace-nowrap">Update</button>
                            </form>

                            <p class="font-bold w-20 text-right shrink-0">Tk {{ number_format($row['subtotal'], 0) }}</p>

                            <form action="{{ route('cart.remove', $row['menuItem']->id) }}" method="POST" class="shrink-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition w-7 h-7 flex items-center justify-center rounded-full hover:bg-red-50">✕</button>
                            </form>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="lg:col-span-1">
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 sticky top-24">
                    <h2 class="font-bold mb-3">Order Summary</h2>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Subtotal</span>
                        <span>Tk {{ number_format($total, 0) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600 mt-1.5">
                        <span>Delivery fee</span>
                        <span>Tk {{ number_format($deliveryFee, 0) }}</span>
                    </div>
                    <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t border-gray-100">
                        <span>Total</span>
                        <span>Tk {{ number_format($total + $deliveryFee, 0) }}</span>
                    </div>

                    <form action="{{ route('cart.checkout') }}" method="POST" class="mt-5">
                        @csrf
                        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2.5 rounded-full transition">
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection
