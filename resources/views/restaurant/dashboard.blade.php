@extends('layouts.dashboard', ['portal' => 'Partner'])

@section('title', 'Dashboard - ' . $restaurant->name)

@section('header-actions')
    <a href="{{ route('restaurant.profile.edit') }}" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Edit profile</a>
    <form action="{{ route('restaurant.logout') }}" method="POST">
        @csrf
        <button type="submit" class="text-sm font-medium bg-white/10 hover:bg-white/20 transition px-3.5 py-1.5 rounded-full">Logout</button>
    </form>
@endsection

@section('content')
    @if ($restaurant->is_removed_by_admin)
        <div class="mb-6 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl px-4 py-4 text-sm">
            <div class="flex items-start gap-3 mb-3">
                <span class="text-lg shrink-0">🚫</span>
                <p class="font-semibold">Your restaurant is no longer listed on the app. Contact the authority if you believe this is a mistake.</p>
            </div>

            <form action="{{ route('restaurant.messages.store') }}" method="POST" class="flex items-start gap-2">
                @csrf
                <textarea name="body" rows="2" required placeholder="Write a message to the FoodHub team…"
                    class="flex-1 border border-red-200 dark:border-red-800 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-red-400 transition"></textarea>
                <button type="submit" class="text-sm font-semibold bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded-full transition whitespace-nowrap">Send message</button>
            </form>

            @if ($restaurant->messages->isNotEmpty())
                <div class="mt-3 pt-3 border-t border-red-200 dark:border-red-800 space-y-1.5">
                    @foreach ($restaurant->messages as $message)
                        <p class="text-xs text-red-700 dark:text-red-400">
                            <span class="font-semibold">{{ $message->status === 'resolved' ? '✓ Resolved' : 'Sent' }}</span>
                            &middot; {{ $message->created_at->diffForHumans() }}: {{ $message->body }}
                        </p>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    @unless ($restaurant->is_approved)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⏳</span>
            <p>Your restaurant is pending admin approval. It won't be visible to customers until approved.</p>
        </div>
    @endunless

    @if ($pendingUpdateRequest)
        <div class="mb-6 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">📝</span>
            <p>You have a profile change request awaiting admin review.</p>
        </div>
    @endif

    @if ($restaurant->adminEdits->isNotEmpty())
        <div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl px-4 py-4">
            <div class="flex items-start gap-3 mb-2">
                <span class="text-lg shrink-0">🛠️</span>
                <p class="font-semibold text-blue-800 dark:text-blue-300 text-sm">Recent changes made by the FoodHub admin</p>
            </div>
            <ul class="space-y-1.5 pl-1">
                @foreach ($restaurant->adminEdits->take(8) as $edit)
                    <li class="text-sm text-blue-700 dark:text-blue-400 flex items-center gap-2">
                        @unless ($edit->seen_by_restaurant)
                            <span class="text-[10px] font-bold uppercase bg-blue-600 text-white px-1.5 py-0.5 rounded shrink-0">New</span>
                        @endunless
                        <span>{{ $edit->summary }}</span>
                        <span class="text-xs text-blue-400 dark:text-blue-500">&middot; {{ $edit->created_at->diffForHumans() }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mb-6 flex items-center gap-4">
        <div class="w-14 h-14 shrink-0 rounded-xl bg-gradient-to-br from-stone-700 to-rose-950 flex items-center justify-center text-2xl text-white overflow-hidden">
            @if ($restaurant->logo)
                <img src="{{ asset('uploads/' . $restaurant->logo) }}" alt="Logo" class="w-full h-full object-cover">
            @else
                🍽️
            @endif
        </div>
        <div>
            <h1 class="text-2xl font-bold flex items-center gap-2">
                {{ $restaurant->name }}
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $restaurant->isCurrentlyOpen() ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                    {{ $restaurant->isCurrentlyOpen() ? 'Open' : 'Closed' }}
                </span>
            </h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm">Owner: {{ $restaurant->owner_name }} &middot; {{ $restaurant->email }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 mb-8 flex items-center justify-between gap-4 flex-wrap">
        <form action="{{ route('restaurant.hours.update') }}" method="POST" class="flex items-end gap-3 flex-wrap">
            @csrf
            @method('PUT')
            <div>
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Opening time</label>
                <input type="time" name="opening_time" value="{{ substr($restaurant->opening_time ?? '10:00', 0, 5) }}" required
                    class="block mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>
            <div>
                <label class="text-xs font-medium text-gray-500 dark:text-gray-400">Closing time</label>
                <input type="time" name="closing_time" value="{{ substr($restaurant->closing_time ?? '22:00', 0, 5) }}" required
                    class="block mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
            </div>
            <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-2 rounded-full transition">Save hours</button>
        </form>

        <form action="{{ route('restaurant.toggle-closed') }}" method="POST">
            @csrf
            <button type="submit" class="text-sm font-semibold px-4 py-2 rounded-full transition {{ $restaurant->is_manually_closed ? 'bg-green-600 hover:bg-green-700 text-white' : 'bg-red-50 dark:bg-red-900/30 hover:bg-red-100 text-red-700 dark:text-red-400' }}">
                {{ $restaurant->is_manually_closed ? 'Reopen restaurant' : 'Close restaurant now' }}
            </button>
        </form>
    </div>

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Rating</p>
            <p class="text-2xl font-bold mt-1">★ {{ $restaurant->rating }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Menu categories</p>
            <p class="text-2xl font-bold mt-1">{{ $restaurant->categories->count() }}</p>
        </div>
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
            <p class="text-xs text-gray-400 dark:text-gray-500 uppercase tracking-wide">Menu items</p>
            <p class="text-2xl font-bold mt-1">{{ $restaurant->categories->sum(fn ($c) => $c->menuItems->count()) }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between mb-3">
        <h2 class="text-lg font-bold">Incoming orders</h2>
    </div>
    @if ($incomingOrders->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-6 text-center text-gray-500 dark:text-gray-400 mb-8">
            No incoming orders right now.
        </div>
    @else
        <div class="space-y-3 mb-8">
            @foreach ($incomingOrders as $order)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center justify-between gap-3 flex-wrap">
                    <div class="min-w-0">
                        <p class="font-semibold truncate">Order #{{ $order->id }} <span class="text-gray-400 dark:text-gray-500 font-normal font-mono text-xs">#{{ $order->tracking_code }}</span></p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->items->count() }} items &middot; Tk {{ number_format($order->total, 0) }}</p>
                        <span class="text-xs font-semibold bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-full">{{ $order->statusLabel() }}</span>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        @if ($order->status === \App\Models\Order::PLACED)
                            <form action="{{ route('restaurant.orders.accept', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-2 rounded-full transition">Accept order</button>
                            </form>
                        @elseif ($order->status === \App\Models\Order::RESTAURANT_ACCEPTED)
                            <form action="{{ route('restaurant.orders.preparing', $order) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-2 rounded-full transition">Start preparing</button>
                            </form>
                        @elseif ($order->status === \App\Models\Order::PREPARING)
                            <span class="text-sm text-gray-500 dark:text-gray-400">{{ $order->rider ? 'Rider: ' . $order->rider->name : 'Waiting for a rider to accept pickup…' }}</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="flex items-center justify-between mb-3 mt-6">
        <h2 class="text-lg font-bold">Menu</h2>
        <a href="{{ route('restaurant.menu-items.create') }}" class="text-sm font-semibold bg-rose-950 hover:bg-rose-900 text-white px-3.5 py-1.5 rounded-full transition">+ Add menu item</a>
    </div>

    @if ($restaurant->categories->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-8 text-center text-gray-500 dark:text-gray-400">
            You haven't added any menu items yet.
        </div>
    @else
        @foreach ($restaurant->categories as $category)
            <h3 class="text-sm font-bold uppercase tracking-wide text-gray-400 dark:text-gray-500 mb-2 mt-5">{{ $category->name }}</h3>
            <div class="grid sm:grid-cols-2 gap-3">
                @foreach ($category->menuItems as $item)
                    <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4 flex items-center gap-3">
                        <div class="w-12 h-12 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center text-xl overflow-hidden">
                            @if ($item->image)
                                <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                🍲
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-medium truncate">{{ $item->name }}</p>
                            <p class="text-sm font-bold text-gray-800 dark:text-gray-200">Tk {{ number_format($item->price, 0) }}</p>
                            @unless ($item->is_approved)
                                <span class="text-[10px] font-bold uppercase bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded">Pending approval</span>
                            @endunless
                        </div>
                        <a href="{{ route('restaurant.menu-items.edit', $item) }}" class="text-xs font-semibold text-rose-800 dark:text-rose-400 hover:underline shrink-0">Edit</a>
                    </div>
                @endforeach
            </div>
        @endforeach
    @endif
@endsection
