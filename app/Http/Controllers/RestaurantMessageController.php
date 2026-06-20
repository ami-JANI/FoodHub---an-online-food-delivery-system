<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantMessageController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        Auth::guard('restaurant')->user()->messages()->create([
            'body' => $data['body'],
        ]);

        return redirect()->route('restaurant.dashboard')
            ->with('status', 'Your message has been sent to the FoodHub team.');
    }
}
