@extends('layouts.app')

@section('title', 'Order Placed - FoodHub')

@section('content')
    <div class="text-center py-16">
        <div class="text-5xl mb-4">✅</div>
        <h1 class="text-2xl font-bold mb-2">Order placed successfully!</h1>
        <p class="text-gray-500 mb-6">Thanks for ordering with FoodHub. Your food is on the way.</p>
        <a href="{{ route('home') }}" class="bg-pink-600 hover:bg-pink-700 text-white font-medium px-5 py-2 rounded-full">
            Back to restaurants
        </a>
    </div>
@endsection
