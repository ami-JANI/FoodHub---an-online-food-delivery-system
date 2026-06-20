<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use App\Models\RestaurantMessage;
use App\Models\RestaurantUpdateRequest;
use App\Models\Rider;
use Illuminate\Http\Request;

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

    public function removeRestaurant(Restaurant $restaurant)
    {
        $restaurant->update(['is_removed_by_admin' => true]);

        return back()->with('status', "{$restaurant->name} has been removed from the app. The owner can still sign in to their dashboard.");
    }

    public function restoreRestaurant(Restaurant $restaurant)
    {
        $restaurant->update(['is_removed_by_admin' => false]);

        return back()->with('status', "{$restaurant->name} has been restored and is visible to customers again.");
    }

    public function resolveRestaurantMessage(RestaurantMessage $message)
    {
        $message->update(['status' => 'resolved']);

        return back()->with('status', 'Message marked as resolved.');
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

    public function approveRider(Request $request, Rider $rider)
    {
        $data = $request->validate([
            'hourly_wage' => ['required', 'numeric', 'min:0'],
        ]);

        $rider->update([
            'is_approved' => true,
            'hourly_wage' => $data['hourly_wage'],
        ]);

        return back()->with('status', "{$rider->name} has been approved as a rider with an hourly wage of Tk {$data['hourly_wage']}.");
    }

    public function rejectRider(Rider $rider)
    {
        $rider->delete();

        return back()->with('status', 'Rider application was rejected and removed.');
    }
}
