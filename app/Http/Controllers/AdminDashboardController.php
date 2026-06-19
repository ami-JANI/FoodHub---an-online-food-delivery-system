<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $restaurantCount = Restaurant::count();
        $userCount = User::count();
        $restaurants = Restaurant::latest()->take(10)->get();

        return view('admin.dashboard', compact('restaurantCount', 'userCount', 'restaurants'));
    }
}
