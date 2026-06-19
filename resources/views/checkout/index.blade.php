@extends('layouts.app')

@section('title', 'Checkout - FoodHub')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Checkout</h1>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
                @csrf

                <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5">
                    <h2 class="font-bold mb-1">Delivery address</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Choose a saved address or add a new one. Address and phone number are required to place an order.</p>

                    @if ($errors->any())
                        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-400 px-3 py-2 text-sm">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="space-y-2.5">
                        @foreach ($addresses as $address)
                            <label class="flex items-start gap-3 border border-gray-200 dark:border-gray-700 rounded-lg p-3 cursor-pointer hover:border-rose-400 transition">
                                <input type="radio" name="address_choice" value="{{ $address->id }}"
                                    {{ $address->is_default || $loop->first ? 'checked' : '' }}
                                    class="address-choice-radio mt-1" data-address-id="{{ $address->id }}">
                                <div class="min-w-0">
                                    <p class="font-semibold text-sm flex items-center gap-2">
                                        {{ $address->label }}
                                        @if ($address->is_default)
                                            <span class="text-[10px] font-bold uppercase bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-1.5 py-0.5 rounded">Default</span>
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ $address->address_line }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">📞 {{ $address->phone }}</p>
                                </div>
                            </label>
                        @endforeach

                        <label class="flex items-start gap-3 border border-gray-200 dark:border-gray-700 rounded-lg p-3 cursor-pointer hover:border-rose-400 transition">
                            <input type="radio" name="address_choice" value="new"
                                {{ $addresses->isEmpty() ? 'checked' : '' }}
                                class="address-choice-radio mt-1" id="new-address-radio">
                            <p class="font-semibold text-sm">➕ Use a new address</p>
                        </label>
                    </div>

                    <div id="new-address-fields" class="mt-4 space-y-3 {{ $addresses->isNotEmpty() ? 'hidden' : '' }}">
                        <div>
                            <label class="text-sm font-medium">Delivery address</label>
                            <textarea name="address_line" rows="2" placeholder="House, road, area, city..."
                                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">{{ old('address_line') }}</textarea>
                        </div>
                        <div>
                            <label class="text-sm font-medium">Phone number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="01XXXXXXXXX"
                                class="w-full mt-1 border border-gray-200 dark:border-gray-700 dark:bg-gray-800 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-800 focus:border-rose-800 transition">
                        </div>

                        @include('partials.map-picker', ['mapId' => 'checkout-map'])

                        <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                            <input type="checkbox" name="save_address" value="1" checked>
                            Save this address to my profile
                        </label>
                    </div>
                </div>
            </form>
        </div>

        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-100 dark:border-gray-800 shadow-sm p-5 sticky top-24">
                <h2 class="font-bold mb-3">Order from {{ $restaurant->name }}</h2>
                <div class="space-y-1.5 mb-3 pb-3 border-b border-gray-100 dark:border-gray-800">
                    @foreach ($items as $row)
                        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                            <span>{{ $row['qty'] }}× {{ $row['menuItem']->name }}</span>
                            <span>Tk {{ number_format($row['subtotal'], 0) }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Subtotal</span>
                    <span>Tk {{ number_format($total, 0) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mt-1.5">
                    <span>Delivery fee</span>
                    <span>Tk {{ number_format($deliveryFee, 0) }}</span>
                </div>
                <div class="flex justify-between font-bold text-lg mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <span>Total</span>
                    <span>Tk {{ number_format($total + $deliveryFee, 0) }}</span>
                </div>

                <button type="submit" form="checkout-form" class="w-full mt-5 bg-rose-950 hover:bg-rose-900 text-white font-semibold py-2.5 rounded-full transition">
                    Place Order
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var radios = document.querySelectorAll('.address-choice-radio');
            var newFields = document.getElementById('new-address-fields');

            function toggleNewFields() {
                var selected = document.querySelector('.address-choice-radio:checked');
                newFields.classList.toggle('hidden', !selected || selected.value !== 'new');
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', toggleNewFields);
            });
            toggleNewFields();

            var savedAddresses = @json($addresses->map(fn ($a) => ['id' => $a->id, 'line' => $a->address_line, 'phone' => $a->phone]));

            document.getElementById('checkout-form').addEventListener('submit', function (event) {
                var selected = document.querySelector('.address-choice-radio:checked');
                var addressText, phoneText;

                if (selected && selected.value !== 'new') {
                    var match = savedAddresses.find(function (a) { return String(a.id) === selected.value; });
                    addressText = match ? match.line : '';
                    phoneText = match ? match.phone : '';
                } else {
                    addressText = document.querySelector('[name="address_line"]').value;
                    phoneText = document.querySelector('[name="phone"]').value;
                }

                if (!addressText.trim() || !phoneText.trim()) {
                    alert('Please provide a delivery address and phone number before placing your order.');
                    event.preventDefault();
                    return;
                }

                var message = 'Please confirm your delivery details:\n\nAddress: ' + addressText + '\nPhone: ' + phoneText + '\n\nIs this correct?';

                if (!confirm(message)) {
                    event.preventDefault();
                }
            });
        })();
    </script>
@endsection
