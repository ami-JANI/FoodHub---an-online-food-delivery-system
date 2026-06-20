<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class RestaurantAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.restaurant-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('restaurant')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('restaurant.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.restaurant-register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'restaurant_name' => ['required', 'string', 'max:255'],
            'owner_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:restaurants,email'],
            'phone' => ['required', 'string', 'max:30'],
            'cuisine' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'address_line' => ['required', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'logo' => ['nullable', 'image', 'max:4096'],
            'cover_image' => ['nullable', 'image', 'max:4096'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $slug = Str::slug($data['restaurant_name']);
        $originalSlug = $slug;
        $count = 1;
        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = "{$originalSlug}-{$count}";
            $count++;
        }

        $restaurant = Restaurant::create([
            'name' => $data['restaurant_name'],
            'slug' => $slug,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'owner_name' => $data['owner_name'],
            'phone' => $data['phone'],
            'cuisine' => $data['cuisine'] ?? null,
            'description' => $data['description'] ?? null,
            'address_line' => $data['address_line'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'logo' => $request->hasFile('logo') ? $request->file('logo')->store('restaurants', 'uploads') : null,
            'cover_image' => $request->hasFile('cover_image') ? $request->file('cover_image')->store('restaurants', 'uploads') : null,
            'is_open' => true,
            'is_approved' => false,
        ]);

        Auth::guard('restaurant')->login($restaurant);

        return redirect()->route('restaurant.dashboard')
            ->with('status', 'Your restaurant has been submitted and is pending admin approval.');
    }

    public function logout(Request $request)
    {
        Auth::guard('restaurant')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('restaurant.login');
    }
}
