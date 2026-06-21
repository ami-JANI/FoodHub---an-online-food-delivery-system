<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show()
    {
        $user = Auth::guard('web')->user()->load('addresses');
        $orders = $user->orders()->with('restaurant')->take(10)->get();
        $favorites = $user->favorites()->with('restaurant')->get();

        return view('account.show', [
            'user' => $user,
            'addresses' => $user->addresses,
            'orders' => $orders,
            'favorites' => $favorites,
        ]);
    }
}
