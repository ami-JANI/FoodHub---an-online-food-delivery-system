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
        'latitude', 'longitude', 'subtotal', 'delivery_fee', 'total', 'status',
        'accepted_at', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'accepted_at' => 'datetime',
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

    public function statusLabel(): string
    {
        return self::STEPS[$this->status] ?? $this->status;
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
