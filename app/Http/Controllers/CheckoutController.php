<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        ['items' => $items, 'total' => $total, 'restaurant' => $restaurant] = $this->cartSummary();

        if (empty($items) || ! $restaurant) {
            return redirect()->route('cart.index');
        }

        if ($total < $restaurant->minimum_order) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'This restaurant requires a minimum order of Tk '.number_format($restaurant->minimum_order, 0).'. Add more items to checkout.',
            ]);
        }

        $user = Auth::guard('web')->user();
        $previewAddress = $user->addresses->firstWhere('is_default', true) ?? $user->addresses->first();

        $deliveryFee = $previewAddress
            ? $restaurant->deliveryFeeFor($previewAddress->latitude, $previewAddress->longitude)
            : $restaurant->deliveryFeeFor(Session::get('user_lat'), Session::get('user_lng'));

        return view('checkout.index', [
            'items' => $items,
            'total' => $total,
            'deliveryFee' => $deliveryFee,
            'restaurant' => $restaurant,
            'addresses' => $user->addresses,
        ]);
    }

    public function store(Request $request)
    {
        ['items' => $items, 'total' => $total, 'restaurant' => $restaurant] = $this->cartSummary();

        if (empty($items) || ! $restaurant) {
            return redirect()->route('cart.index');
        }

        if ($total < $restaurant->minimum_order) {
            return redirect()->route('cart.index')->withErrors([
                'cart' => 'This restaurant requires a minimum order of Tk '.number_format($restaurant->minimum_order, 0).'. Add more items to checkout.',
            ]);
        }

        $user = Auth::guard('web')->user();

        $request->merge([
            'address_id' => $request->input('address_choice') !== 'new' ? $request->input('address_choice') : null,
        ]);

        $data = $request->validate([
            'address_id' => ['nullable', 'exists:addresses,id'],
            'address_line' => ['required_without:address_id', 'nullable', 'string', 'max:500'],
            'phone' => ['required_without:address_id', 'nullable', 'string', 'max:30'],
            'latitude' => ['required_without:address_id', 'nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['required_without:address_id', 'nullable', 'numeric', 'between:-180,180'],
            'save_address' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['address_id'])) {
            $address = $user->addresses()->findOrFail($data['address_id']);
            $addressLine = $address->address_line;
            $phone = $address->phone;
            $latitude = $address->latitude;
            $longitude = $address->longitude;
        } else {
            $addressLine = $data['address_line'];
            $phone = $data['phone'];
            $latitude = $data['latitude'];
            $longitude = $data['longitude'];

            if ($request->boolean('save_address')) {
                $user->addresses()->create([
                    'label' => 'Address',
                    'address_line' => $addressLine,
                    'phone' => $phone,
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'is_default' => $user->addresses()->doesntExist(),
                ]);
            }
        }

        $deliveryFee = $restaurant->deliveryFeeFor($latitude, $longitude);

        $order = DB::transaction(function () use ($user, $restaurant, $addressLine, $phone, $latitude, $longitude, $total, $deliveryFee, $items) {
            $order = Order::create([
                'user_id' => $user->id,
                'restaurant_id' => $restaurant->id,
                'tracking_code' => Order::generateTrackingCode(),
                'address_line' => $addressLine,
                'phone' => $phone,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'subtotal' => $total,
                'delivery_fee' => $deliveryFee,
                'total' => $total + $deliveryFee,
                'status' => Order::PLACED,
            ]);

            foreach ($items as $row) {
                $order->items()->create([
                    'menu_item_id' => $row['menuItem']->id,
                    'name' => $row['menuItem']->name,
                    'price' => $row['menuItem']->price,
                    'quantity' => $row['qty'],
                ]);
            }

            return $order;
        });

        Session::forget('cart');

        return redirect()->route('cart.success', $order)->with('order', $order->id);
    }

    public function success(Order $order)
    {
        abort_unless($order->user_id === Auth::guard('web')->id(), 403);

        return view('cart.success', ['order' => $order->load('items', 'restaurant')]);
    }

    private function cartSummary(): array
    {
        $cart = Session::get('cart', []);
        $items = [];
        $total = 0;

        foreach ($cart as $itemId => $qty) {
            $menuItem = MenuItem::find($itemId);
            if (! $menuItem) {
                continue;
            }
            $subtotal = $menuItem->price * $qty;
            $total += $subtotal;
            $items[] = [
                'menuItem' => $menuItem,
                'qty' => $qty,
                'subtotal' => $subtotal,
            ];
        }

        $restaurant = $items[0]['menuItem']->restaurant ?? null;

        return ['items' => $items, 'total' => $total, 'restaurant' => $restaurant];
    }
}
