<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\RestaurantUpdateRequest;

class AdminApprovalController extends Controller
{
    public function approveRestaurant(Restaurant $restaurant)
    {
        $restaurant->update(['is_approved' => true]);

        return back()->with('status', "{$restaurant->name} has been approved and is now live.");
    }

    public function rejectRestaurant(Restaurant $restaurant)
    {
        $restaurant->delete();

        return back()->with('status', 'Restaurant registration was rejected and removed.');
    }

    public function approveMenuItem(MenuItem $menuItem)
    {
        $menuItem->update(['is_approved' => true]);

        return back()->with('status', "\"{$menuItem->name}\" has been approved.");
    }

    public function rejectMenuItem(MenuItem $menuItem)
    {
        $menuItem->delete();

        return back()->with('status', 'Menu item was rejected and removed.');
    }

    public function approveProfileUpdate(RestaurantUpdateRequest $updateRequest)
    {
        $restaurant = $updateRequest->restaurant;

        $restaurant->update(array_filter([
            'name' => $updateRequest->name,
            'cuisine' => $updateRequest->cuisine,
            'description' => $updateRequest->description,
            'address_line' => $updateRequest->address_line,
            'latitude' => $updateRequest->latitude,
            'longitude' => $updateRequest->longitude,
            'logo' => $updateRequest->logo,
            'cover_image' => $updateRequest->cover_image,
        ], fn ($value) => $value !== null));

        $updateRequest->update(['status' => 'approved', 'reviewed_at' => now()]);

        return back()->with('status', "Profile changes for {$restaurant->name} have been approved.");
    }

    public function rejectProfileUpdate(RestaurantUpdateRequest $updateRequest)
    {
        $updateRequest->update(['status' => 'rejected', 'reviewed_at' => now()]);

        return back()->with('status', 'Profile change request was rejected.');
    }
}
