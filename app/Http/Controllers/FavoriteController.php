<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function toggle(Restaurant $restaurant)
    {
        $user = Auth::guard('web')->user();

        $favorite = $user->favorites()->where('restaurant_id', $restaurant->id)->first();

        if ($favorite) {
            $favorite->delete();
            $favorited = false;
        } else {
            $user->favorites()->create(['restaurant_id' => $restaurant->id]);
            $favorited = true;
        }

        if (request()->wantsJson()) {
            return response()->json(['favorited' => $favorited]);
        }

        return back()->with('status', $favorited ? "Added {$restaurant->name} to your favorites." : "Removed {$restaurant->name} from your favorites.");
    }
}
