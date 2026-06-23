<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RiderDashboardController extends Controller
{
    public function index()
    {
        /** @var Rider $rider */
        $rider = Auth::guard('rider')->user();

        $riderLat = Session::get('rider_lat');
        $riderLng = Session::get('rider_lng');
        $hasLocation = $riderLat !== null && $riderLng !== null;

        $nearbyOrders = collect();

        if ($rider->is_approved && $hasLocation) {
            $nearbyOrders = Order::whereNull('rider_id')
                ->where('status', Order::PREPARING)
                ->with('restaurant', 'items')
                ->get()
                ->map(function (Order $order) use ($riderLat, $riderLng) {
                    $order->pickup_distance_km = $order->pickupDistanceFromKm($riderLat, $riderLng);
                    $order->route_distance_km = $order->routeDistanceKm($riderLat, $riderLng);
                    $order->rider_earning = $order->riderEarning($riderLat, $riderLng);

                    return $order;
                })
                ->filter(fn (Order $order) => $order->pickup_distance_km !== null && $order->pickup_distance_km <= Rider::PICKUP_RADIUS_KM)
                ->sortBy('pickup_distance_km')
                ->values();
        }

        $activeDeliveries = $rider->orders()
            ->whereIn('status', [Order::RIDER_ASSIGNED, Order::RIDER_ARRIVED, Order::PICKED_UP, Order::ON_THE_WAY])
            ->with('restaurant', 'items')
            ->get();

        $pastDeliveries = $rider->orders()->where('status', Order::DELIVERED)->with('restaurant')->take(10)->get();

        return view('rider.dashboard', [
            'rider' => $rider,
            'hasLocation' => $hasLocation,
            'nearbyOrders' => $nearbyOrders,
            'activeDeliveries' => $activeDeliveries,
            'pastDeliveries' => $pastDeliveries,
        ]);
    }

    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        /** @var Rider $rider */
        $rider = Auth::guard('rider')->user();
        $riderLat = Session::get('rider_lat') ?? $rider->last_latitude;
        $riderLng = Session::get('rider_lng') ?? $rider->last_longitude;

        return view('rider.order', [
            'order' => $order->load('items', 'restaurant', 'messages'),
            'riderLat' => $riderLat,
            'riderLng' => $riderLng,
        ]);
    }

    public function updateLocation(Request $request)
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
        ]);

        Session::put('rider_lat', $data['latitude']);
        Session::put('rider_lng', $data['longitude']);

        Auth::guard('rider')->user()->update([
            'last_latitude' => $data['latitude'],
            'last_longitude' => $data['longitude'],
            'last_seen_at' => now(),
        ]);

        return response()->json(['status' => 'ok']);
    }

    public function accept(Order $order)
    {
        abort_unless(Auth::guard('rider')->user()->is_approved, 403, 'Your account is not approved yet.');
        abort_unless($order->rider_id === null && $order->status === Order::PREPARING, 409, 'This order has already been picked up by another rider.');

        $order->update([
            'rider_id' => Auth::guard('rider')->id(),
            'status' => Order::RIDER_ASSIGNED,
            'accepted_at' => now(),
        ]);

        return back()->with('status', "Order #{$order->id} accepted. Head to {$order->restaurant->name} for pickup.");
    }

    public function arrived(Order $order)
    {
        $this->authorizeOrder($order);
        abort_unless($order->status === Order::RIDER_ASSIGNED, 409);

        $order->update(['status' => Order::RIDER_ARRIVED]);

        return back()->with('status', "Marked as arrived at {$order->restaurant->name}.");
    }

    public function pickedUp(Order $order)
    {
        $this->authorizeOrder($order);
        abort_unless($order->status === Order::RIDER_ARRIVED, 409);

        $order->update(['status' => Order::PICKED_UP]);

        return back()->with('status', "Order #{$order->id} picked up. Head to the customer's address.");
    }

    public function onTheWay(Order $order)
    {
        $this->authorizeOrder($order);
        abort_unless($order->status === Order::PICKED_UP, 409);

        $order->update(['status' => Order::ON_THE_WAY]);

        return back()->with('status', "Order #{$order->id} is now on the way.");
    }

    public function complete(Order $order)
    {
        $this->authorizeOrder($order);

        $order->update([
            'status' => Order::DELIVERED,
            'delivered_at' => now(),
        ]);

        return back()->with('status', "Order #{$order->id} marked as delivered.");
    }

    public function postMessage(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $order->messages()->create([
            'sender_type' => 'rider',
            'body' => $data['body'],
        ]);

        return back();
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless($order->rider_id === Auth::guard('rider')->id(), 403);
    }
}
