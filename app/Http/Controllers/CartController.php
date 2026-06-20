<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function index()
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
        $deliveryFee = $restaurant
            ? $restaurant->deliveryFeeFor(Session::get('user_lat'), Session::get('user_lng'))
            : 0;

        return view('cart.index', [
            'items' => $items,
            'total' => $total,
            'deliveryFee' => $deliveryFee,
            'restaurant' => $restaurant,
        ]);
    }

    public function add(Request $request, MenuItem $menuItem)
    {
        if (! $menuItem->restaurant->isCurrentlyOpen()) {
            return back()->withErrors(['cart' => 'This restaurant is currently unavailable, so this item cannot be added to your cart.']);
        }

        $cart = Session::get('cart', []);

        $currentRestaurantId = $this->cartRestaurantId($cart);

        if ($currentRestaurantId && $currentRestaurantId !== $menuItem->restaurant_id) {
            $cart = [];
        }

        $cart[$menuItem->id] = ($cart[$menuItem->id] ?? 0) + 1;
        Session::put('cart', $cart);

        return back()->with('status', "{$menuItem->name} added to cart.");
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $qty = max(0, (int) $request->input('qty', 1));
        $cart = Session::get('cart', []);

        if ($qty === 0) {
            unset($cart[$menuItem->id]);
        } else {
            $cart[$menuItem->id] = $qty;
        }

        Session::put('cart', $cart);

        return back();
    }

    public function remove(MenuItem $menuItem)
    {
        $cart = Session::get('cart', []);
        unset($cart[$menuItem->id]);
        Session::put('cart', $cart);

        return back();
    }

    private function cartRestaurantId(array $cart): ?int
    {
        if (empty($cart)) {
            return null;
        }

        $firstItemId = array_key_first($cart);
        $menuItem = MenuItem::find($firstItemId);

        return $menuItem?->restaurant_id;
    }
}
