<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RestaurantOrderController extends Controller
{
    public function accept(Order $order)
    {
        $this->authorizeOrder($order);
        abort_unless($order->status === Order::PLACED, 409);

        $order->update(['status' => Order::RESTAURANT_ACCEPTED]);

        return back()->with('status', "Order #{$order->id} accepted.");
    }

    public function preparing(Order $order)
    {
        $this->authorizeOrder($order);
        abort_unless($order->status === Order::RESTAURANT_ACCEPTED, 409);

        $order->update(['status' => Order::PREPARING]);

        return back()->with('status', "Order #{$order->id} is now being prepared.");
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->restaurant_id === Auth::guard('restaurant')->id(), 403);
    }
}
