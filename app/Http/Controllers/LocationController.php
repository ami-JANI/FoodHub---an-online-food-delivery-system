<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        Session::put('user_lat', $data['latitude']);
        Session::put('user_lng', $data['longitude']);

        return response()->json(['status' => 'ok']);
    }

    public function clear()
    {
        Session::forget(['user_lat', 'user_lng']);

        return response()->json(['status' => 'ok']);
    }
}
