<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Order extends Model
{
    public const PLACED = 'placed';

    public const RESTAURANT_ACCEPTED = 'restaurant_accepted';

    public const PREPARING = 'preparing';

    public const RIDER_ASSIGNED = 'rider_assigned';

    public const RIDER_ARRIVED = 'rider_arrived';

    public const PICKED_UP = 'picked_up';

    public const ON_THE_WAY = 'on_the_way';

    public const DELIVERED = 'delivered';

    public const CANCELLED = 'cancelled';

    /** What a rider earns per kilometer of the delivery route. */
    public const EARNING_RATE_PER_KM = 5.0;

    /** Minutes an order waits for a rider to accept before it is auto-cancelled. */
    public const RIDER_SEARCH_TIMEOUT_MINUTES = 15;

    /** Cancellation reason shown when no rider accepts the order in time. */
    public const NO_RIDER_REASON = 'No rider found currently to deliver your order, so it was cancelled.';

    /** Message the customer sees when an admin cancels their order. */
    public const ADMIN_CANCEL_CUSTOMER_MESSAGE = 'Something went wrong with this order. Please contact our support team for help.';

    /** Message a restaurant or rider sees when an admin cancels the order. */
    public const ADMIN_CANCEL_PARTNER_MESSAGE = 'This order was cancelled because the customer violated our company policy.';

    public const STEPS = [
        self::PLACED => 'Order placed',
        self::RESTAURANT_ACCEPTED => 'Restaurant accepted your order',
        self::PREPARING => 'Preparing your meal',
        self::RIDER_ASSIGNED => 'Rider assigned',
        self::RIDER_ARRIVED => 'Rider arrived at restaurant',
        self::PICKED_UP => 'Rider picked up your order',
        self::ON_THE_WAY => 'Rider is on the way',
        self::DELIVERED => 'Delivered',
    ];

    protected $fillable = [
        'user_id', 'restaurant_id', 'rider_id', 'tracking_code', 'address_line', 'phone',
        'latitude', 'longitude', 'subtotal', 'delivery_fee', 'total', 'status', 'cancellation_reason', 'cancelled_by',
        'accepted_at', 'preparing_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'accepted_at' => 'datetime',
            'preparing_at' => 'datetime',
            'delivered_at' => 'datetime',
        ];
    }

    public static function generateTrackingCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (self::where('tracking_code', $code)->exists());

        return $code;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(OrderMessage::class)->oldest();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    /**
     * Distance in km between a rider's coordinates and this order's pickup point (the restaurant).
     */
    public function pickupDistanceFromKm(?float $lat, ?float $lng): ?float
    {
        return $this->restaurant->distanceFromKm($lat, $lng);
    }

    /**
     * The two delivery legs in km: rider → restaurant (pickup), then restaurant → customer (drop-off).
     */
    public function routeLegsKm(?float $riderLat, ?float $riderLng): array
    {
        return [
            'to_restaurant' => $this->restaurant->distanceFromKm($riderLat, $riderLng),
            'to_customer' => $this->restaurant->distanceFromKm($this->latitude, $this->longitude),
        ];
    }

    /**
     * Total route distance in km (both legs). Null when any coordinate is missing.
     */
    public function routeDistanceKm(?float $riderLat, ?float $riderLng): ?float
    {
        $legs = $this->routeLegsKm($riderLat, $riderLng);

        if ($legs['to_restaurant'] === null || $legs['to_customer'] === null) {
            return null;
        }

        return round($legs['to_restaurant'] + $legs['to_customer'], 2);
    }

    /**
     * What the rider earns for this delivery: total route distance × the per-km rate.
     */
    public function riderEarning(?float $riderLat, ?float $riderLng): ?float
    {
        $distance = $this->routeDistanceKm($riderLat, $riderLng);

        return $distance !== null ? round($distance * self::EARNING_RATE_PER_KM, 2) : null;
    }

    public function statusLabel(): string
    {
        if ($this->status === self::CANCELLED) {
            return 'Order cancelled';
        }

        return self::STEPS[$this->status] ?? $this->status;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::CANCELLED;
    }

    public function wasCancelledByAdmin(): bool
    {
        return $this->status === self::CANCELLED && $this->cancelled_by === 'admin';
    }

    /**
     * The cancellation notice shown to a restaurant/rider (partner side).
     */
    public function partnerCancellationNotice(): ?string
    {
        if (! $this->isCancelled()) {
            return null;
        }

        return $this->wasCancelledByAdmin() ? self::ADMIN_CANCEL_PARTNER_MESSAGE : 'This order was cancelled.';
    }

    public function isOngoing(): bool
    {
        return ! in_array($this->status, [self::DELIVERED, self::CANCELLED], true);
    }

    /**
     * A customer may cancel only before the restaurant starts preparing the meal.
     */
    public function canBeCancelledByCustomer(): bool
    {
        return in_array($this->status, [self::PLACED, self::RESTAURANT_ACCEPTED], true);
    }

    /**
     * Auto-cancel an order that has been waiting for a rider too long with none assigned.
     * Returns true if it was cancelled by this call.
     */
    public function autoCancelIfNoRider(): bool
    {
        if ($this->status !== self::PREPARING || $this->rider_id !== null || ! $this->preparing_at) {
            return false;
        }

        if ($this->preparing_at->diffInMinutes(now()) < self::RIDER_SEARCH_TIMEOUT_MINUTES) {
            return false;
        }

        $this->update([
            'status' => self::CANCELLED,
            'cancellation_reason' => self::NO_RIDER_REASON,
        ]);

        return true;
    }

    /**
     * Ordered list of [key, label, isComplete, isCurrent] for the tracking timeline.
     */
    public function statusTimeline(): array
    {
        $keys = array_keys(self::STEPS);
        $currentIndex = array_search($this->status, $keys, true);

        return array_map(function ($key, $index) use ($currentIndex) {
            return [
                'key' => $key,
                'label' => self::STEPS[$key],
                'complete' => $currentIndex !== false && $index <= $currentIndex,
                'current' => $key === $this->status,
            ];
        }, $keys, array_keys($keys));
    }
}
