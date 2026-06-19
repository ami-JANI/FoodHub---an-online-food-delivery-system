<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class RestaurantDashboardController extends Controller
{
    public function index()
    {
        $restaurant = Auth::guard('restaurant')->user()->load('categories.menuItems');
        $pendingUpdateRequest = $restaurant->pendingUpdateRequest();

        $incomingOrders = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', [Order::PLACED, Order::RESTAURANT_ACCEPTED, Order::PREPARING])
            ->with('items', 'rider')
            ->latest()
            ->get();

        return view('restaurant.dashboard', compact('restaurant', 'pendingUpdateRequest', 'incomingOrders'));
    }
}
