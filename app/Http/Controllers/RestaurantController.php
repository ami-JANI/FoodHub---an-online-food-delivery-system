<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::orderByDesc('rating')->get();

        return view('restaurants.index', compact('restaurants'));
    }

    public function show(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->with('categories.menuItems')
            ->firstOrFail();

        return view('restaurants.show', compact('restaurant'));
    }
}
