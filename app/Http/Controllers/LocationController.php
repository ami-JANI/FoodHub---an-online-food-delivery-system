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
        Session::forget(['user_lat', 'user_lng', 'user_location_label']);

        return response()->json(['status' => 'ok']);
    }

    public function showManual()
    {
        return view('location.set', [
            'lat' => Session::get('user_lat'),
            'lng' => Session::get('user_lng'),
            'label' => Session::get('user_location_label'),
        ]);
    }

    public function setManual(Request $request)
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'label' => ['nullable', 'string', 'max:255'],
        ]);

        Session::put('user_lat', $data['latitude']);
        Session::put('user_lng', $data['longitude']);
        Session::put('user_location_label', $data['label'] ?? null);

        return redirect()->route('home')->with('status', 'Your delivery location has been set.');
    }
}
