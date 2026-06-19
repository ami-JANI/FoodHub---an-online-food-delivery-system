@extends('layouts.app')

@section('title', 'Out of delivery area - FoodHub')

@section('content')
    <div class="text-center bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm py-16 px-6 max-w-md mx-auto">
        <div class="text-5xl mb-4">📍</div>
        <h1 class="text-xl font-bold mb-2">Out of delivery area</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-6">{{ $exception->getMessage() ?: 'This restaurant is outside your delivery area.' }}</p>
        <a href="{{ route('home') }}" class="bg-rose-950 hover:bg-rose-900 text-white font-semibold px-5 py-2.5 rounded-full inline-block transition">
            Back to restaurants
        </a>
    </div>
@endsection
