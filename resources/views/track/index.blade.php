@extends('layouts.app')

@section('title', 'Track Order - FoodHub')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Track Order</h1>

    <h2 class="text-lg font-bold mb-3">Running orders</h2>
    @if ($runningOrders->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
            You have no orders in progress.
        </div>
    @else
        <div class="space-y-3 mb-8">
            @foreach ($runningOrders as $order)
                <a href="{{ route('track.show', $order->tracking_code) }}" class="block bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 hover:border-rose-400 transition">
                    <div class="flex items-center justify-between gap-3">
                        <div class="min-w-0">
                            <p class="font-semibold truncate">{{ $order->restaurant->name }} <span class="text-gray-400 dark:text-gray-500 font-normal font-mono text-xs">#{{ $order->tracking_code }}</span></p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tk {{ number_format($order->total, 0) }} &middot; {{ $order->created_at->format('d M Y, h:i A') }}</p>
                        </div>
                        <span class="shrink-0 text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2.5 py-1 rounded-full whitespace-nowrap">{{ $order->statusLabel() }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    @if ($pastOrders->isNotEmpty())
        <h2 class="text-lg font-bold mb-3">Past orders</h2>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($pastOrders as $order)
                <a href="{{ route('track.show', $order->tracking_code) }}" class="p-4 flex items-center justify-between gap-3 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition">
                    <p class="font-medium">{{ $order->restaurant->name }} <span class="text-gray-400 dark:text-gray-500 font-normal font-mono text-xs">#{{ $order->tracking_code }}</span></p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Tk {{ number_format($order->total, 0) }}</p>
                </a>
            @endforeach
        </div>
    @endif
@endsection
