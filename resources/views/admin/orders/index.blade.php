@extends('layouts.dashboard', ['portal' => 'Admin'])

@section('title', 'All orders - FoodHub')

@section('header-actions')
    <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Dashboard</a>
@endsection

@section('content')
    <a href="{{ route('admin.dashboard') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; Back to dashboard</a>

    <h1 class="text-2xl font-bold mt-3 mb-6">All orders</h1>

    <h2 class="text-lg font-bold mb-3">Running orders <span class="text-gray-400 dark:text-gray-500 font-normal text-sm">({{ $runningOrders->count() }})</span></h2>
    @if ($runningOrders->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
            No running orders right now.
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800 mb-8">
            @foreach ($runningOrders as $order)
                <div class="p-4 flex items-start justify-between gap-3 flex-wrap">
                    <div class="min-w-0 text-sm">
                        <p class="font-semibold">Order #{{ $order->id }} <span class="font-mono text-xs text-gray-400 dark:text-gray-500">#{{ $order->tracking_code }}</span>
                            <span class="text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-full ml-1">{{ $order->statusLabel() }}</span>
                        </p>
                        <p class="text-gray-600 dark:text-gray-300">👤 {{ $order->user->name }} ({{ $order->user->email }}) → 📍 {{ $order->address_line }}</p>
                        <p class="text-gray-600 dark:text-gray-300">🏪 {{ $order->restaurant->name }} &middot; 🛵 {{ $order->rider->name ?? 'No rider yet' }} &middot; Tk {{ number_format($order->total, 0) }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">🕒 {{ $order->created_at->format('d M Y, h:i A') }}</p>
                    </div>
                    <form action="{{ route('admin.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Cancel order #{{ $order->id }}? The customer, restaurant and rider will be notified.')" class="shrink-0">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-xs font-semibold bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400 px-3 py-1.5 rounded-full transition">Cancel order</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif

    <h2 class="text-lg font-bold mb-3">Past orders <span class="text-gray-400 dark:text-gray-500 font-normal text-sm">({{ $pastOrders->count() }})</span></h2>
    @if ($pastOrders->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400">
            No past orders yet.
        </div>
    @else
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm divide-y divide-gray-100 dark:divide-gray-800">
            @foreach ($pastOrders as $order)
                <div class="p-4 flex items-start justify-between gap-3 flex-wrap">
                    <div class="min-w-0 text-sm">
                        <p class="font-semibold">Order #{{ $order->id }} <span class="font-mono text-xs text-gray-400 dark:text-gray-500">#{{ $order->tracking_code }}</span>
                            <span class="text-xs font-semibold {{ $order->isCancelled() ? 'bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-green-50 dark:bg-green-900/30 text-green-700 dark:text-green-400' }} px-2 py-0.5 rounded-full ml-1">{{ $order->statusLabel() }}</span>
                        </p>
                        <p class="text-gray-600 dark:text-gray-300">👤 {{ $order->user->name }} ({{ $order->user->email }}) → 📍 {{ $order->address_line }}</p>
                        <p class="text-gray-600 dark:text-gray-300">🏪 {{ $order->restaurant->name }} &middot; 🛵 {{ $order->rider->name ?? '—' }} &middot; Tk {{ number_format($order->total, 0) }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500">🕒 {{ $order->created_at->format('d M Y, h:i A') }}</p>
                        @if ($order->review)
                            <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">★ {{ $order->review->rating }}/5 review attached — will be deleted with this order</p>
                        @endif
                    </div>
                    <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Permanently delete order #{{ $order->id }} and its review? This cannot be undone.')" class="shrink-0">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-xs font-semibold bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400 px-3 py-1.5 rounded-full transition">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    @endif
@endsection
