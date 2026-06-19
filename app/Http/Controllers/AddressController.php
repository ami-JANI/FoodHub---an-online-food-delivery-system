<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function create()
    {
        return view('account.addresses.form', ['address' => null]);
    }

    public function store(Request $request)
    {
        $data = $this->validateAddress($request);

        $user = Auth::guard('web')->user();

        if ($data['make_default'] || $user->addresses()->doesntExist()) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $user->addresses()->create($data);

        return redirect()->route('account.show')->with('status', 'Address saved.');
    }

    public function edit(Address $address)
    {
        $this->authorizeAddress($address);

        return view('account.addresses.form', ['address' => $address]);
    }

    public function update(Request $request, Address $address)
    {
        $this->authorizeAddress($address);

        $data = $this->validateAddress($request);
        $user = Auth::guard('web')->user();

        if ($data['make_default']) {
            $user->addresses()->update(['is_default' => false]);
            $data['is_default'] = true;
        }

        $address->update($data);

        return redirect()->route('account.show')->with('status', 'Address updated.');
    }

    public function destroy(Address $address)
    {
        $this->authorizeAddress($address);
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            Auth::guard('web')->user()->addresses()->first()?->update(['is_default' => true]);
        }

        return redirect()->route('account.show')->with('status', 'Address removed.');
    }

    public function setDefault(Address $address)
    {
        $this->authorizeAddress($address);

        Auth::guard('web')->user()->addresses()->update(['is_default' => false]);
        $address->update(['is_default' => true]);

        return redirect()->route('account.show')->with('status', 'Default address updated.');
    }

    private function validateAddress(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'address_line' => ['required', 'string', 'max:500'],
            'phone' => ['required', 'string', 'max:30'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $data['make_default'] = $request->boolean('make_default');

        return $data;
    }

    private function authorizeAddress(Address $address): void
    {
        abort_unless($address->user_id === Auth::guard('web')->id(), 403);
    }
}
