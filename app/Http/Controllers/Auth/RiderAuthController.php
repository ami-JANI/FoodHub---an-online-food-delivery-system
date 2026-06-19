<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RiderAuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.rider-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::guard('rider')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'These credentials do not match our records.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('rider.dashboard'));
    }

    public function showRegister()
    {
        return view('auth.rider-register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:riders,email'],
            'educational_qualification' => ['required', 'in:JSC,SSC,HSC,Other'],
            'educational_qualification_other' => ['required_if:educational_qualification,Other', 'nullable', 'string', 'max:255'],
            'vehicle_type' => ['required', 'in:Cycle,Motorcycle,Other'],
            'vehicle_type_other' => ['required_if:vehicle_type,Other', 'nullable', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $qualification = $data['educational_qualification'] === 'Other'
            ? $data['educational_qualification_other']
            : $data['educational_qualification'];

        $vehicle = $data['vehicle_type'] === 'Other'
            ? $data['vehicle_type_other']
            : $data['vehicle_type'];

        $rider = Rider::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'educational_qualification' => $qualification,
            'vehicle_type' => $vehicle,
            'is_approved' => false,
        ]);

        Auth::guard('rider')->login($rider);

        return redirect()->route('rider.dashboard')
            ->with('status', 'Your rider application has been submitted and is pending admin approval.');
    }

    public function logout(Request $request)
    {
        Auth::guard('rider')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('rider.login');
    }
}
