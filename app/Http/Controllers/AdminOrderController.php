<?php

namespace App\Http\Controllers;

use App\Models\Order;

class AdminOrderController extends Controller
{
    public function index()
    {
        $runningOrders = Order::whereNotIn('status', [Order::DELIVERED, Order::CANCELLED])
            ->with('user', 'restaurant', 'rider')
            ->latest()
            ->get();

        $pastOrders = Order::whereIn('status', [Order::DELIVERED, Order::CANCELLED])
            ->with('user', 'restaurant', 'rider', 'review')
            ->latest()
            ->take(100)
            ->get();

        return view('admin.orders.index', compact('runningOrders', 'pastOrders'));
    }

    public function cancel(Order $order)
    {
        abort_unless($order->isOngoing(), 409, 'Only ongoing orders can be cancelled.');

        $order->update([
            'status' => Order::CANCELLED,
            'cancelled_by' => 'admin',
            'cancellation_reason' => Order::ADMIN_CANCEL_CUSTOMER_MESSAGE,
        ]);

        return back()->with('status', "Order #{$order->id} has been cancelled.");
    }

    public function destroy(Order $order)
    {
        abort_if($order->isOngoing(), 409, 'Ongoing orders cannot be deleted — cancel them first.');

        // Order items, messages and the review are removed automatically via cascade.
        $order->delete();

        return back()->with('status', "Order #{$order->id} and its review were permanently deleted.");
    }
}
