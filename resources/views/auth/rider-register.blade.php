@extends('layouts.auth')

@php($maxWidth = 'max-w-md')

@section('title', 'Become a Rider - FoodHub')

@section('content')
    <h1 class="text-xl font-bold mb-1">Become a FoodHub rider</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">Deliver orders near you and earn an hourly wage.</p>

    @if ($errors->any())
        <div class="mb-4 rounded bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-2 text-sm">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('rider.register') }}" class="space-y-3">
        @csrf
        <div>
            <label class="text-sm font-medium">Full name</label>
            <input type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Phone</label>
            <input type="text" name="phone" value="{{ old('phone') }}" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>

        <div>
            <label class="text-sm font-medium">Educational qualification</label>
            <select name="educational_qualification" id="qualification-select" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                <option value="">— Select —</option>
                @foreach (['JSC', 'SSC', 'HSC', 'Other'] as $option)
                    <option value="{{ $option }}" {{ old('educational_qualification') === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <input type="text" name="educational_qualification_other" id="qualification-other"
                value="{{ old('educational_qualification_other') }}" placeholder="Please specify"
                class="w-full mt-2 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition {{ old('educational_qualification') === 'Other' ? '' : 'hidden' }}">
        </div>

        <div>
            <label class="text-sm font-medium">Vehicle</label>
            <select name="vehicle_type" id="vehicle-select" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                <option value="">— Select —</option>
                @foreach (['Cycle', 'Motorcycle', 'Other'] as $option)
                    <option value="{{ $option }}" {{ old('vehicle_type') === $option ? 'selected' : '' }}>{{ $option }}</option>
                @endforeach
            </select>
            <input type="text" name="vehicle_type_other" id="vehicle-other"
                value="{{ old('vehicle_type_other') }}" placeholder="Please specify"
                class="w-full mt-2 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition {{ old('vehicle_type') === 'Other' ? '' : 'hidden' }}">
        </div>

        <div>
            <label class="text-sm font-medium">Password</label>
            <input type="password" name="password" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>
        <div>
            <label class="text-sm font-medium">Confirm Password</label>
            <input type="password" name="password_confirmation" required
                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
        </div>

        <p class="text-xs text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg px-3 py-2">
            ⚠️ Your application will be reviewed by an admin, who will also set your hourly wage before you can start accepting deliveries.
        </p>

        <button type="submit" class="w-full bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
            Apply to become a rider
        </button>
    </form>

    <p class="text-sm text-gray-500 dark:text-gray-400 mt-4 text-center">
        Already a rider? <a href="{{ route('rider.login') }}" class="text-rose-800 dark:text-rose-400 hover:underline">Sign in</a>
    </p>

    <script>
        function toggleOther(selectId, otherId) {
            var select = document.getElementById(selectId);
            var other = document.getElementById(otherId);
            select.addEventListener('change', function () {
                other.classList.toggle('hidden', select.value !== 'Other');
            });
        }
        toggleOther('qualification-select', 'qualification-other');
        toggleOther('vehicle-select', 'vehicle-other');
    </script>
@endsection
