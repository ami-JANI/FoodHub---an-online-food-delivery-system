<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\RestaurantMessage;
use App\Models\RestaurantUpdateRequest;
use App\Models\Rider;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $restaurantCount = Restaurant::count();
        $userCount = User::count();
        $restaurants = Restaurant::where('is_approved', true)->latest()->take(10)->get();

        $pendingRestaurants = Restaurant::where('is_approved', false)->latest()->get();
        $pendingMenuItems = MenuItem::where('is_approved', false)->with('restaurant', 'category')->latest()->get();
        $pendingProfileUpdates = RestaurantUpdateRequest::where('status', 'pending')->with('restaurant')->latest()->get();
        $pendingRiders = Rider::where('is_approved', false)->latest()->get();
        $openRestaurantMessages = RestaurantMessage::where('status', 'open')->with('restaurant')->latest()->get();

        return view('admin.dashboard', compact(
            'restaurantCount', 'userCount', 'restaurants',
            'pendingRestaurants', 'pendingMenuItems', 'pendingProfileUpdates', 'pendingRiders',
            'openRestaurantMessages'
        ));
    }
}
