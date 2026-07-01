<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantHoursController extends Controller
{
    public function update(Request $request)
    {
        $data = $request->validate([
            'opening_time' => ['required', 'date_format:H:i'],
            'closing_time' => ['required', 'date_format:H:i'],
            'delivery_time' => ['required', 'string', 'max:50'],
        ]);

        Auth::guard('restaurant')->user()->update([
            'opening_time' => $data['opening_time'],
            'closing_time' => $data['closing_time'],
            'delivery_time' => $data['delivery_time'],
        ]);

        return back()->with('status', 'Store settings updated.');
    }

    public function toggleClosed()
    {
        $restaurant = Auth::guard('restaurant')->user();

        $closingNow = ! $restaurant->is_manually_closed;

        $restaurant->update([
            'is_manually_closed' => $closingNow,
            // Reopening is a manual override too, so it stays open even outside business hours.
            'is_manually_opened' => ! $closingNow,
        ]);

        return back()->with('status', $closingNow
            ? 'Your restaurant is now closed to new orders.'
            : 'Your restaurant is open again.');
    }
}
