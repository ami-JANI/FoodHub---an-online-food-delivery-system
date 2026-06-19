@extends('layouts.app')

@section('title', 'Order Placed - FoodHub')

@section('content')
    <div class="text-center bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm py-12 px-6 max-w-md mx-auto">
        <div class="w-16 h-16 mx-auto rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center text-3xl mb-4">✅</div>
        <h1 class="text-2xl font-bold mb-2">Order placed successfully!</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">Thanks for ordering from {{ $order->restaurant->name }}. Your food is on the way.</p>

        <div class="text-left bg-gray-50 dark:bg-gray-800 rounded-xl p-4 mb-6">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide mb-1">Order #{{ $order->id }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-1">📍 {{ $order->address_line }}</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-3">📞 {{ $order->phone }}</p>
            <div class="space-y-1 border-t border-gray-200 dark:border-gray-700 pt-3">
                @foreach ($order->items as $item)
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                        <span>{{ $item->quantity }}× {{ $item->name }}</span>
                        <span>Tk {{ number_format($item->price * $item->quantity, 0) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex justify-between font-bold mt-3 pt-3 border-t border-gray-200 dark:border-gray-700">
                <span>Total</span>
                <span>Tk {{ number_format($order->total, 0) }}</span>
            </div>
        </div>

        <a href="{{ route('home') }}" class="bg-rose-950 hover:bg-rose-900 text-white font-semibold px-5 py-2.5 rounded-full inline-block transition">
            Back to restaurants
        </a>
    </div>
@endsection
