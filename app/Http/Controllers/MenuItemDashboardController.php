<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuItemDashboardController extends Controller
{
    public function create()
    {
        $restaurant = Auth::guard('restaurant')->user();

        return view('restaurant.menu-items.form', [
            'menuItem' => null,
            'categories' => $restaurant->categories,
        ]);
    }

    public function store(Request $request)
    {
        $restaurant = Auth::guard('restaurant')->user();
        $data = $this->validateData($request);

        $categoryId = $this->resolveCategory($restaurant, $data);

        $menuItem = $restaurant->menuItems()->create([
            'category_id' => $categoryId,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'is_available' => $request->boolean('is_available', true),
            'is_approved' => false,
            'image' => $this->storeImage($request),
        ]);

        return redirect()->route('restaurant.dashboard')
            ->with('status', "\"{$menuItem->name}\" was added and is pending admin approval.");
    }

    public function edit(MenuItem $menuItem)
    {
        $this->authorizeItem($menuItem);

        $restaurant = Auth::guard('restaurant')->user();

        return view('restaurant.menu-items.form', [
            'menuItem' => $menuItem,
            'categories' => $restaurant->categories,
        ]);
    }

    public function update(Request $request, MenuItem $menuItem)
    {
        $this->authorizeItem($menuItem);

        $restaurant = Auth::guard('restaurant')->user();
        $data = $this->validateData($request);

        $categoryId = $this->resolveCategory($restaurant, $data);

        $menuItem->fill([
            'category_id' => $categoryId,
            'name' => $data['name'],
            'description' => $data['description'],
            'price' => $data['price'],
            'is_available' => $request->boolean('is_available', true),
            'is_approved' => false,
        ]);

        if ($image = $this->storeImage($request)) {
            $menuItem->image = $image;
        }

        $menuItem->save();

        return redirect()->route('restaurant.dashboard')
            ->with('status', "\"{$menuItem->name}\" was updated and is pending admin approval before it goes live again.");
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'integer'],
            'new_category' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:4096'],
        ]);
    }

    private function resolveCategory($restaurant, array $data): int
    {
        if (! empty($data['new_category'])) {
            $category = $restaurant->categories()->firstOrCreate(
                ['name' => $data['new_category']],
                ['sort_order' => $restaurant->categories()->count()]
            );

            return $category->id;
        }

        $category = Category::where('id', $data['category_id'] ?? null)
            ->where('restaurant_id', $restaurant->id)
            ->first();

        abort_unless($category, 422, 'Please choose or create a category.');

        return $category->id;
    }

    private function storeImage(Request $request): ?string
    {
        if (! $request->hasFile('image')) {
            return null;
        }

        return $request->file('image')->store('menu-items', 'uploads');
    }

    private function authorizeItem(MenuItem $menuItem): void
    {
        abort_unless($menuItem->restaurant_id === Auth::guard('restaurant')->id(), 403);
    }
}
