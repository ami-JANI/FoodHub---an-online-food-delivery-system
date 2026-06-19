<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantProfileController extends Controller
{
    public function edit()
    {
        $restaurant = Auth::guard('restaurant')->user();
        $pendingUpdateRequest = $restaurant->pendingUpdateRequest();

        return view('restaurant.profile.edit', compact('restaurant', 'pendingUpdateRequest'));
    }

    public function update(Request $request)
    {
        $restaurant = Auth::guard('restaurant')->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cuisine' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address_line' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
        ]);

        $logo = $request->hasFile('logo') ? $request->file('logo')->store('restaurants', 'uploads') : null;
        $coverImage = $request->hasFile('cover_image') ? $request->file('cover_image')->store('restaurants', 'uploads') : null;

        // Replace any existing pending request rather than queueing duplicates.
        $restaurant->updateRequests()->where('status', 'pending')->delete();

        $restaurant->updateRequests()->create([
            'name' => $data['name'],
            'cuisine' => $data['cuisine'] ?? null,
            'description' => $data['description'] ?? null,
            'address_line' => $data['address_line'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'logo' => $logo,
            'cover_image' => $coverImage,
            'status' => 'pending',
        ]);

        return redirect()->route('restaurant.dashboard')
            ->with('status', 'Your profile changes have been submitted for admin approval.');
    }
}
