@extends('layouts.app')

@section('title', $restaurant->name . ' - FoodHub')

@section('content')
    @php($open = $restaurant->isCurrentlyOpen())

    <a href="{{ route('home') }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; All restaurants</a>

    @unless ($open)
        <div class="mt-4 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">🚫</span>
            <p>This restaurant is currently unavailable. You can browse the menu, but ordering is disabled until it reopens.</p>
        </div>
    @endunless

    @error('cart')
        <div class="mt-4 flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⚠️</span>
            <p>{{ $message }}</p>
        </div>
    @enderror

    @if ($cartConflict)
        <div class="mt-4 flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 rounded-xl px-4 py-3 text-sm">
            <span class="text-lg shrink-0">⚠️</span>
            <p>
                You already have items from <span class="font-semibold">{{ $cartConflict->name }}</span> in your cart.
                Adding something from <span class="font-semibold">{{ $restaurant->name }}</span> will clear those items and start a new order.
            </p>
        </div>
    @endif

    <div class="mt-4 mb-8 rounded-2xl text-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-stone-900 to-rose-950">
            @if ($restaurant->cover_image)
                <img src="{{ asset('uploads/' . $restaurant->cover_image) }}" alt="{{ $restaurant->name }} banner" class="w-full h-full object-cover opacity-50">
            @endif
        </div>
        <div class="relative px-6 py-8">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-4">
                    <div class="relative shrink-0">
                        <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-white shadow bg-stone-800 flex items-center justify-center">
                            @if ($restaurant->logo)
                                <img src="{{ asset('uploads/' . $restaurant->logo) }}" alt="{{ $restaurant->name }} logo" class="w-full h-full object-cover">
                            @else
                                <span class="text-2xl">🍽️</span>
                            @endif
                        </div>
                        @unless ($open)
                            <div class="absolute inset-0 rounded-full bg-black/70 flex items-center justify-center text-center px-1">
                                <span class="text-[10px] font-bold leading-tight">Closed<br>Now</span>
                            </div>
                        @endunless
                    </div>
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold">{{ $restaurant->name }}</h1>
                        <p class="text-stone-300 mt-1">{{ $restaurant->cuisine }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <button type="button" id="favorite-btn" data-restaurant-id="{{ $restaurant->id }}"
                        class="w-9 h-9 rounded-full border border-white/30 hover:bg-white/10 transition flex items-center justify-center text-lg {{ $isFavorited ? 'text-rose-400' : 'text-white' }}"
                        title="{{ $isFavorited ? 'Remove from favorites' : 'Add to favorites' }}">
                        {{ $isFavorited ? '❤️' : '🤍' }}
                    </button>
                    @if ($restaurant->latitude && $restaurant->longitude)
                        <a href="https://www.google.com/maps/dir/?api=1&destination={{ $restaurant->latitude }},{{ $restaurant->longitude }}" target="_blank" rel="noopener"
                            class="w-9 h-9 rounded-full border border-white/30 hover:bg-white/10 transition flex items-center justify-center text-lg" title="Get directions">
                            🧭
                        </a>
                    @endif
                    <button type="button" id="share-btn" data-name="{{ $restaurant->name }}"
                        class="w-9 h-9 rounded-full border border-white/30 hover:bg-white/10 transition flex items-center justify-center text-lg" title="Share">
                        🔗
                    </button>
                </div>
            </div>

            <div class="flex items-center gap-3 text-sm mt-4 flex-wrap">
                <span class="flex items-center gap-1 bg-green-600 text-white font-semibold px-2 py-0.5 rounded-md">★ {{ $restaurant->averageRating() }}</span>
                <a href="#reviews" class="text-stone-200 hover:text-white hover:underline">{{ $restaurant->reviewCount() }} Ratings</a>
                <span class="text-stone-500">|</span>
                <a href="#reviews" class="text-stone-200 hover:text-white hover:underline">{{ $restaurant->reviewCount() }} Reviews</a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-3 mb-8 text-center">
        <div>
            <p class="text-2xl font-bold">{{ $restaurant->positiveReviewPercentage() }}%</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Positive Review</p>
        </div>
        <div>
            <p class="text-2xl font-bold">{{ $restaurant->delivery_time }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Delivery Time</p>
        </div>
        <div>
            <p class="text-2xl font-bold">Tk {{ number_format($restaurant->minimum_order, 0) }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-400">Minimum Order</p>
        </div>
    </div>

    @if ($restaurant->categories->isNotEmpty())
        <div class="flex items-center gap-2 overflow-x-auto pb-2 mb-2 sticky top-[68px] bg-gray-50/95 dark:bg-gray-950/95 backdrop-blur z-[5] -mx-1 px-1">
            @foreach ($restaurant->categories as $category)
                <a href="#category-{{ $category->id }}"
                   class="shrink-0 text-sm font-medium bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 hover:border-rose-400 hover:text-rose-800 dark:hover:text-rose-400 transition px-3.5 py-1.5 rounded-full">
                    {{ $category->name }}
                </a>
            @endforeach
        </div>
    @endif

    @foreach ($restaurant->categories as $category)
        <h2 id="category-{{ $category->id }}" class="text-lg font-bold mb-3 mt-8 scroll-mt-32">{{ $category->name }}</h2>
        <div class="grid sm:grid-cols-2 gap-3">
            @foreach ($category->menuItems as $item)
                <div class="group bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-md transition p-4 flex items-center gap-4">
                    <div class="w-16 h-16 shrink-0 rounded-lg bg-gradient-to-br from-stone-200 to-amber-100 dark:from-stone-800 dark:to-stone-700 flex items-center justify-center text-2xl overflow-hidden">
                        @if ($item->image)
                            <img src="{{ asset('uploads/' . $item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                        @else
                            🍲
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold truncate">{{ $item->name }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">{{ $item->description }}</p>
                        <p class="text-sm font-bold mt-1 text-gray-800 dark:text-gray-200">Tk {{ number_format($item->price, 0) }}</p>
                    </div>
                    <form action="{{ route('cart.add', $item->id) }}" method="POST"
                        @class(['shrink-0', 'cart-conflict-form' => $cartConflict, 'closed-restaurant-form' => ! $open])
                        @if ($cartConflict)
                            data-conflict-restaurant="{{ $cartConflict->name }}"
                            data-target-restaurant="{{ $restaurant->name }}"
                        @endif
                    >
                        @csrf
                        <button type="submit"
                            class="text-white text-sm font-semibold px-3.5 py-2 rounded-full whitespace-nowrap transition {{ $open ? 'bg-rose-950 group-hover:bg-rose-900' : 'bg-gray-400 cursor-not-allowed' }}">
                            + Add
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
    @endforeach

    <h2 id="reviews" class="text-lg font-bold mb-3 mt-10 scroll-mt-32">Reviews ({{ $restaurant->reviewCount() }})</h2>
    @if ($restaurant->reviews->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">No reviews yet. Be the first to order and review!</p>
    @else
        <div class="space-y-4">
            @foreach ($restaurant->reviews as $review)
                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-4">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <span class="font-semibold text-sm">{{ $review->reviewerName() }}</span>
                        <span class="text-amber-500 text-sm shrink-0">{{ str_repeat('★', $review->rating) }}{{ str_repeat('☆', 5 - $review->rating) }}</span>
                    </div>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mb-2">{{ $review->created_at->diffForHumans() }}</p>
                    @if ($review->body)
                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $review->body }}</p>
                    @endif
                    @if (! empty($review->photos))
                        <div class="flex items-center gap-2 mt-3">
                            @foreach ($review->photos as $photo)
                                <img src="{{ asset('uploads/' . $photo) }}" alt="Review photo" class="w-20 h-20 rounded-lg object-cover border border-gray-100 dark:border-gray-800">
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    @if ($cartConflict)
        <script>
            document.querySelectorAll('.cart-conflict-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    var from = form.dataset.conflictRestaurant;
                    var to = form.dataset.targetRestaurant;
                    var message = 'Your cart has items from ' + from + '. Adding this will clear your cart and start a new order from ' + to + '. Continue?';

                    if (!confirm(message)) {
                        event.preventDefault();
                    }
                });
            });
        </script>
    @endif

    <script>
        (function () {
            var btn = document.getElementById('favorite-btn');
            if (!btn) return;

            btn.addEventListener('click', function () {
                fetch('{{ route('favorites.toggle', $restaurant->id) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                })
                    .then(function (response) {
                        if (response.status === 401) {
                            window.location.href = '{{ route('login') }}';
                            return null;
                        }
                        return response.json();
                    })
                    .then(function (data) {
                        if (!data) return;
                        btn.textContent = data.favorited ? '❤️' : '🤍';
                        btn.classList.toggle('text-rose-400', data.favorited);
                        btn.classList.toggle('text-white', !data.favorited);
                        btn.title = data.favorited ? 'Remove from favorites' : 'Add to favorites';
                    });
            });
        })();
    </script>

    @unless ($open)
        <script>
            document.querySelectorAll('.closed-restaurant-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();
                    alert('This restaurant is currently unavailable, so you cannot add items to your cart right now.');
                });
            });
        </script>
    @endunless
@endsection
