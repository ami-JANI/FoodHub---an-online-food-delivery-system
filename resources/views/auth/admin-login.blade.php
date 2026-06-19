@extends('layouts.auth')

@section('title', 'Admin Sign in - FoodHub')

@section('content')
    <h1 class="text-xl font-bold mb-1">Admin sign in</h1>
    <p class="text-sm text-gray-500 mb-5">Restricted access. Admin accounts are created manually.</p>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 text-red-700 px-3 py-2 text-sm">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-3">
        @csrf
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required
                class="w-full mt-1 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-pink-400 transition">
        </div>
        <button type="submit" class="w-full bg-gray-900 hover:bg-black text-white font-semibold py-2.5 rounded-full transition">
            Sign in
        </button>
    </form>
@endsection
