@extends('layouts.auth')

@section('title', 'Join as a Partner - FoodHub')

@section('content')
    <h1 class="text-xl font-bold mb-1">Join FoodHub as a partner</h1>
    <p class="text-sm text-gray-500 mb-5">Register your restaurant and start receiving orders.</p>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 text-red-700 px-3 py-2 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('restaurant.register') }}" class="space-y-3">
        @csrf
        <div>
            <label class="text-sm font-medium">Restaurant name</label>
            <input type="text" name="restaurant_name" value="{{ old('restaurant_name') }}" required autofocus
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Owner name</label>
            <input type="text" name="owner_name" value="{{ old('owner_name') }}" required
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Cuisine type</label>
            <input type="text" name="cuisine" value="{{ old('cuisine') }}" placeholder="e.g. Bangladeshi, Fast Food"
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <button type="submit" class="w-full bg-pink-600 hover:bg-pink-700 text-white font-semibold py-2.5 rounded-full transition">
            Register restaurant
        </button>
    </form>

    <p class="text-sm text-gray-500 mt-4 text-center">
        Already a partner? <a href="{{ route('restaurant.login') }}" class="text-pink-600 hover:underline">Sign in</a>
    </p>
@endsection
