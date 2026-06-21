<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RestaurantDashboardController extends Controller
{
    public function index()
    {
        $restaurant = Auth::guard('restaurant')->user()->load('categories.menuItems', 'messages', 'adminEdits');
        $pendingUpdateRequest = $restaurant->pendingUpdateRequest();

        $incomingOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', [Order::PLACED, Order::RESTAURANT_ACCEPTED, Order::PREPARING])
            ->with('items', 'rider')
            ->latest()
            ->get();

        // Mark admin-edit notices as seen so the unseen highlight clears on next load.
        $restaurant->adminEdits()->where('seen_by_restaurant', false)->update(['seen_by_restaurant' => true]);

        $adminEmail = Admin::value('email') ?? 'admin@foodhub.test';

        return view('restaurant.dashboard', compact('restaurant', 'pendingUpdateRequest', 'incomingOrders', 'adminEmail'));
    }
}
