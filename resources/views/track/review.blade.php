@extends('layouts.app')

@section('title', 'Review your order - FoodHub')

@section('content')
    <a href="{{ route('track.show', $order->tracking_code) }}" class="text-sm text-gray-500 dark:text-gray-400 hover:text-rose-800 dark:hover:text-rose-400 transition inline-flex items-center gap-1">&larr; Back to order</a>

    <div class="max-w-xl mx-auto mt-4">
        <h1 class="text-2xl font-bold mb-1">Review your order</h1>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-6">{{ $order->restaurant->name }} &middot; #{{ $order->tracking_code }}</p>

        @if ($errors->any())
            <div class="mb-4 flex items-start gap-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 rounded-xl px-4 py-3 text-sm">
                <span class="text-lg shrink-0">⚠️</span>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reviews.store', $order->tracking_code) }}" method="POST" enctype="multipart/form-data"
              class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 space-y-5">
            @csrf

            <div>
                <label class="text-sm font-semibold block mb-2">Your rating</label>
                <div id="star-rating" class="flex items-center gap-1 text-3xl">
                    @for ($i = 1; $i <= 5; $i++)
                        <button type="button" data-value="{{ $i }}" class="star-btn text-gray-300 dark:text-gray-700 hover:scale-110 transition">★</button>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating-input" value="" required>
            </div>

            <div>
                <label for="body" class="text-sm font-semibold block mb-2">Your review (optional)</label>
                <textarea name="body" id="body" rows="4" maxlength="2000" placeholder="Tell others about the food, packaging, delivery time…"
                    class="w-full border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3.5 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ old('body') }}</textarea>
            </div>

            <div>
                <label class="text-sm font-semibold block mb-2">Add photos (up to 3, optional)</label>
                <input type="file" name="photos[]" id="photos-input" accept="image/*" multiple
                    class="block w-full text-sm text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3.5 py-2.5 focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                <p id="photo-hint" class="text-xs text-gray-400 dark:text-gray-500 mt-1"></p>
            </div>

            <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                <input type="checkbox" name="is_anonymous" value="1" class="rounded border-gray-300 dark:border-gray-700 text-rose-800 focus:ring-rose-800">
                Post this review anonymously
            </label>

            <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">Submit review</button>
        </form>
    </div>

    <script>
        (function () {
            var stars = document.querySelectorAll('.star-btn');
            var input = document.getElementById('rating-input');

            function paint(value) {
                stars.forEach(function (star) {
                    var active = parseInt(star.dataset.value, 10) <= value;
                    star.classList.toggle('text-amber-500', active);
                    star.classList.toggle('text-gray-300', !active);
                    star.classList.toggle('dark:text-gray-700', !active);
                });
            }

            stars.forEach(function (star) {
                star.addEventListener('click', function () {
                    input.value = star.dataset.value;
                    paint(parseInt(star.dataset.value, 10));
                });
            });

            var photosInput = document.getElementById('photos-input');
            var hint = document.getElementById('photo-hint');
            photosInput.addEventListener('change', function () {
                if (photosInput.files.length > 3) {
                    hint.textContent = 'You can only attach up to 3 photos. Only the first 3 will be kept.';
                    var dt = new DataTransfer();
                    Array.from(photosInput.files).slice(0, 3).forEach(function (file) { dt.items.add(file); });
                    photosInput.files = dt.files;
                } else {
                    hint.textContent = photosInput.files.length + ' photo(s) selected.';
                }
            });
        })();
    </script>
@endsection
