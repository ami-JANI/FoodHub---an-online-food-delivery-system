<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        [$userLat, $userLng] = $this->userLocation();

        $search = trim((string) $request->query('q', ''));
        $cuisine = $request->query('cuisine');
        $menuCategory = $request->query('menu_category');
        $sort = $request->query('sort', 'rating');

        $query = Restaurant::where('is_approved', true)->where('is_removed_by_admin', false);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('cuisine', 'like', "%{$search}%");
            });
        }

        if ($cuisine) {
            $query->where('cuisine', 'like', "%{$cuisine}%");
        }

        if ($menuCategory) {
            $query->whereHas('categories', function ($q) use ($menuCategory) {
                $q->where('name', $menuCategory);
            });
        }

        $restaurants = $query->get()
            ->map(function (Restaurant $restaurant) use ($userLat, $userLng) {
                $restaurant->distance_km = $restaurant->distanceFromKm($userLat, $userLng);
                $restaurant->computed_delivery_fee = $restaurant->deliveryFeeFor($userLat, $userLng);

                return $restaurant;
            });

        $hasLocation = $userLat !== null && $userLng !== null;

        if ($hasLocation) {
            $restaurants = $restaurants->filter(
                fn (Restaurant $restaurant) => $restaurant->isWithinDeliveryRadius($userLat, $userLng)
            );
        }

        $restaurants = match ($sort) {
            'distance' => $restaurants->sortBy('distance_km'),
            'delivery_fee' => $restaurants->sortBy('computed_delivery_fee'),
            default => $restaurants->sortByDesc('rating'),
        };

        $restaurants = $restaurants->values();

        $cuisines = Restaurant::where('is_approved', true)
            ->where('is_removed_by_admin', false)
            ->pluck('cuisine')
            ->filter()
            ->flatMap(fn ($value) => array_map('trim', explode(',', $value)))
            ->unique()
            ->sort()
            ->values();

        $menuCategories = Category::distinct()->orderBy('name')->pluck('name');

        return view('restaurants.index', [
            'restaurants' => $restaurants,
            'hasLocation' => $hasLocation,
            'userLat' => $userLat,
            'userLng' => $userLng,
            'cuisines' => $cuisines,
            'menuCategories' => $menuCategories,
            'search' => $search,
            'activeCuisine' => $cuisine,
            'activeMenuCategory' => $menuCategory,
            'sort' => $sort,
        ]);
    }

    public function show(string $slug)
    {
        $restaurant = Restaurant::where('slug', $slug)
            ->with(['categories.menuItems' => function ($query) {
                $query->where('is_approved', true)->where('is_available', true);
            }, 'reviews.user'])
            ->firstOrFail();

        abort_unless($restaurant->is_approved && ! $restaurant->is_removed_by_admin, 404);

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
