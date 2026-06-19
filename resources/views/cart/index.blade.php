@extends('layouts.app')

@section('title', 'Your Cart - FoodHub')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Your Cart</h1>

    @if (empty($items))
        <p class="text-gray-500">Your cart is empty. <a href="{{ route('home') }}" class="text-pink-600 hover:underline">Browse restaurants</a>.</p>
    @else
        @if ($restaurant)
            <p class="text-sm text-gray-500 mb-4">Order from <span class="font-medium">{{ $restaurant->name }}</span></p>
        @endif

        <div class="space-y-3">
            @foreach ($items as $row)
                <div class="bg-white rounded-lg border border-gray-100 shadow-sm p-4 flex items-center justify-between gap-3">
                    <div>
                        <h3 class="font-medium">{{ $row['menuItem']->name }}</h3>
                        <p class="text-sm text-gray-500">Tk {{ number_format($row['menuItem']->price, 0) }} each</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <form action="{{ route('cart.update', $row['menuItem']->id) }}" method="POST" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <input type="number" name="qty" value="{{ $row['qty'] }}" min="0" max="20"
                                class="w-16 border border-gray-200 rounded px-2 py-1 text-sm">
                            <button type="submit" class="text-sm text-pink-600 hover:underline">Update</button>
                        </form>

                        <p class="font-semibold w-20 text-right">Tk {{ number_format($row['subtotal'], 0) }}</p>

                        <form action="{{ route('cart.remove', $row['menuItem']->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-sm text-gray-400 hover:text-red-600">✕</button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-6 bg-white rounded-lg border border-gray-100 shadow-sm p-4 max-w-sm ml-auto">
            <div class="flex justify-between text-sm text-gray-600">
                <span>Subtotal</span>
                <span>Tk {{ number_format($total, 0) }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-1">
                <span>Delivery fee</span>
                <span>Tk {{ number_format($deliveryFee, 0) }}</span>
            </div>
            <div class="flex justify-between font-bold text-lg mt-2 pt-2 border-t border-gray-100">
                <span>Total</span>
                <span>Tk {{ number_format($total + $deliveryFee, 0) }}</span>
            </div>

            <form action="{{ route('cart.checkout') }}" method="POST" class="mt-4">
                @csrf
                <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-medium py-2 rounded-full">
                    Place Order
                </button>
            </form>
        </div>
    @endif
@endsection
