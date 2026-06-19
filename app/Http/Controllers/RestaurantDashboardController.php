<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class RestaurantDashboardController extends Controller
{
    public function index()
    {
        $restaurant = Auth::guard('restaurant')->user()->load('categories.menuItems');
        $pendingUpdateRequest = $restaurant->pendingUpdateRequest();

        return view('restaurant.dashboard', compact('restaurant', 'pendingUpdateRequest'));
    }
}
