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
}
