<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class AdminMenuController extends Controller
{
    public function manage(Restaurant $restaurant)
    {
        $restaurant->load('categories.menuItems');

        return view('admin.menu.manage', compact('restaurant'));
    }

    public function updateMenuItem(Request $request, MenuItem $menuItem)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
        ]);

        $menuItem->update($data);

        $menuItem->restaurant->adminEdits()->create([
            'summary' => "Admin edited menu item \"{$menuItem->name}\".",
        ]);

        return back()->with('status', "\"{$menuItem->name}\" was updated.");
    }

    public function deleteMenuItem(MenuItem $menuItem)
    {
        $restaurant = $menuItem->restaurant;
        $name = $menuItem->name;

        $menuItem->delete();

        $restaurant->adminEdits()->create([
            'summary' => "Admin removed menu item \"{$name}\".",
        ]);

        return back()->with('status', "\"{$name}\" was removed.");
    }
}
