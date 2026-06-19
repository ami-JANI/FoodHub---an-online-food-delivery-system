<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Session;

class RestaurantController extends Controller
{
    public function index()
    {
        [$userLat, $userLng] = $this->userLocation();

        $restaurants = Restaurant::where('is_approved', true)->orderByDesc('rating')->get()
            ->map(function (Restaurant $restaurant) use ($userLat, $userLng) {
                $restaurant->distance_km = $restaurant->distanceFromKm($userLat, $userLng);
                $restaurant->computed_delivery_fee = $restaurant->deliveryFeeFor($userLat, $userLng);

                return $restaurant;
            });

        $hasLocation = $userLat !== null && $userLng !== null;

        if ($hasLocation) {
            $restaurants = $restaurants
                ->filter(fn (Restaurant $restaurant) => $restaurant->isWithinDeliveryRadius($userLat, $userLng))
                ->sortBy('distance_km')
                ->values();
        }

        return view('restaurants.index', [
            'restaurants' => $restaurants,
            'hasLocation' => $hasLocation,
        ]);
    }

    public function show(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->with(['categories.menuItems' => function ($query) {
                $query->where('is_approved', true)->where('is_available', true);
            }])
            ->firstOrFail();

        abort_unless($restaurant->is_approved, 404);

        [$userLat, $userLng] = $this->userLocation();

        abort_unless($restaurant->isWithinDeliveryRadius($userLat, $userLng), 403, 'This restaurant is outside your 5km delivery area.');

        $restaurant->distance_km = $restaurant->distanceFromKm($userLat, $userLng);
        $restaurant->computed_delivery_fee = $restaurant->deliveryFeeFor($userLat, $userLng);

        $cartRestaurant = $this->cartRestaurant();
        $cartConflict = $cartRestaurant && $cartRestaurant->id !== $restaurant->id
            ? $cartRestaurant
            : null;

        return view('restaurants.show', compact('restaurant', 'cartConflict'));
    }

    private function userLocation(): array
    {
        return [Session::get('user_lat'), Session::get('user_lng')];
    }

    private function cartRestaurant(): ?Restaurant
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return null;
        }

        $firstItemId = array_key_first($cart);

        return MenuItem::find($firstItemId)?->restaurant;
    }
}
