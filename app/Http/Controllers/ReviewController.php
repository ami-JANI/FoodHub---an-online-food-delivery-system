<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function create(string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)->with('review')->firstOrFail();

        $this->authorizeOrder($order);

        return view('track.review', ['order' => $order]);
    }

    public function store(Request $request, string $trackingCode)
    {
        $order = Order::where('tracking_code', $trackingCode)->with('review')->firstOrFail();

        $this->authorizeOrder($order);

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'body' => ['nullable', 'string', 'max:2000'],
            'is_anonymous' => ['nullable', 'boolean'],
            'photos' => ['nullable', 'array', 'max:3'],
            'photos.*' => ['image', 'max:4096'],
        ]);

        $photoPaths = [];
        foreach ($request->file('photos', []) as $photo) {
            $photoPaths[] = $photo->store('reviews', 'uploads');
        }

        $order->review()->create([
            'restaurant_id' => $order->restaurant_id,
            'user_id' => $order->user_id,
            'rating' => $data['rating'],
            'body' => $data['body'] ?? null,
            'is_anonymous' => $request->boolean('is_anonymous'),
            'photos' => $photoPaths,
        ]);

        return redirect()->route('track.show', $order->tracking_code)
            ->with('status', 'Thanks for your review!');
    }

    private function authorizeOrder(Order $order): void
    {
        abort_unless(Auth::guard('web')->check() && Auth::guard('web')->id() === $order->user_id, 403);
        abort_unless($order->status === Order::DELIVERED, 403, 'Reviews can only be left after delivery.');
        abort_if($order->review, 403, 'You have already reviewed this order.');
    }
}
