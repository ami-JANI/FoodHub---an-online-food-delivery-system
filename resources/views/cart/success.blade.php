@extends('layouts.app')

@section('title', 'Order Placed - FoodHub')

@section('content')
    <div class="text-center bg-white rounded-2xl border border-gray-100 shadow-sm py-16 px-6 max-w-md mx-auto">
        <div class="w-16 h-16 mx-auto rounded-full bg-green-100 flex items-center justify-center text-3xl mb-4">✅</div>
        <h1 class="text-2xl font-bold mb-2">Order placed successfully!</h1>
        <p class="text-gray-500 mb-6">Thanks for ordering with FoodHub. Your food is on the way.</p>
        <a href="{{ route('home') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-semibold px-5 py-2.5 rounded-full inline-block transition">
            Back to restaurants
        </a>
    </div>
@endsection
