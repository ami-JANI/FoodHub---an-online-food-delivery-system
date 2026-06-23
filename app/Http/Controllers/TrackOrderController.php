<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackOrderController extends Controller
{
    public function index()
    {
        $user = Auth::guard('web')->user();

        $runningOrders = $user->orders()
            ->where('status', '!=', Order::DELIVERED)
            ->with('restaurant', 'rider')
            ->get();

        $pastOrders = $user->orders()
            ->where('status', Order::DELIVERED)
            ->with('restaurant')
            ->take(10)
            ->get();

        return view('track.index', compact('runningOrders', 'pastOrders'));
    }

    public function show(string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)
            ->with('items', 'restaurant', 'rider', 'messages', 'review')
            ->firstOrFail();

        $isOwner = Auth::guard('web')->check() && Auth::guard('web')->id() === $order->user_id;

        return view('track.show', [
            'order' => $order,
            'isOwner' => $isOwner,
        ]);
    }

    public function postMessage(Request $request, string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)->firstOrFail();

        abort_unless(Auth::guard('web')->check() && Auth::guard('web')->id() === $order->user_id, 403);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
        ]);

        $order->messages()->create([
            'sender_type' => 'customer',
            'body' => $data['body'],
        ]);

        return back();
    }

    public function cancel(string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)->firstOrFail();

        abort_unless(Auth::guard('web')->check() && Auth::guard('web')->id() === $order->user_id, 403);
        abort_unless($order->canBeCancelledByCustomer(), 403, 'This order can no longer be cancelled.');

        $order->update(['status' => Order::CANCELLED]);

        return redirect()->route('track.show', $order->tracking_code)
            ->with('status', 'Your order has been cancelled.');
    }

    public function riderLocation(string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)->with('rider')->firstOrFail();

        if (! $order->rider) {
            return response()->json(['rider' => null]);
        }

        return response()->json([
            'rider' => [
                'latitude' => $order->rider->last_latitude,
                'longitude' => $order->rider->last_longitude,
                'last_seen_at' => $order->rider->last_seen_at?->diffForHumans(),
            ],
            'status' => $order->status,
            'status_label' => $order->statusLabel(),
        ]);
    }

    public function messages(string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)->with('messages')->firstOrFail();

        return response()->json([
            'messages' => $order->messages->map(fn ($m) => [
                'sender_type' => $m->sender_type,
                'body' => $m->body,
                'created_at' => $m->created_at->format('h:i A'),
            ]),
        ]);
    }
}
