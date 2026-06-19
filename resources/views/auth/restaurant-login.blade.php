@extends('layouts.auth')

@section('title', 'Restaurant Sign in - FoodHub')

@section('content')
    <h1 class="text-xl font-bold mb-1">Restaurant partner sign in</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Manage your restaurant on FoodHub.</p>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-2 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('restaurant.login') }}" class="space-y-3">
        @csrf
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>
        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
            <input type="checkbox" name="remember"> Remember me
        </label>
        <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
            Sign in
        </button>
    </form>

    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 text-center">
        New restaurant? <a href="{{ route('restaurant.register') }}" class="text-rose-800 dark:text-rose-400 hover:underline">Join as a partner</a>
    </p>
@endsection
