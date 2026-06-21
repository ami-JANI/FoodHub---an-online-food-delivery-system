<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Restaurant extends Authenticatable
{
    use Notifiable;

    public const VISIBILITY_RADIUS_KM = 5.0;

    public const DELIVERY_RATE_PER_KM = 3.0;

    protected $fillable = [
        'name', 'slug', 'email', 'password', 'owner_name', 'phone',
        'cuisine', 'description', 'address_line', 'logo', 'cover_image', 'latitude', 'longitude',
        'rating', 'delivery_time', 'delivery_fee', 'is_open', 'is_approved', 'is_removed_by_admin',
        'opening_time', 'closing_time', 'is_manually_closed', 'is_manually_opened',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'latitude' => 'float',
            'longitude' => 'float',
            'is_approved' => 'boolean',
            'is_removed_by_admin' => 'boolean',
            'is_manually_closed' => 'boolean',
            'is_manually_opened' => 'boolean',
        ];
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class)->orderBy('sort_order');
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(MenuItem::class);
    }

    public function updateRequests(): HasMany
    {
        return $this->hasMany(RestaurantUpdateRequest::class)->latest();
    }

    public function pendingUpdateRequest(): ?RestaurantUpdateRequest
    {
        return $this->updateRequests()->where('status', 'pending')->first();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(RestaurantMessage::class)->latest();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviewCount(): int
    {
        return $this->reviews()->count();
    }

    public function averageRating(): float
    {
        $average = $this->reviews()->avg('rating');

        return $average !== null ? round($average, 1) : (float) $this->rating;
    }

    /**
     * Great-circle distance in kilometers between the restaurant and the given coordinates.
     */
    public function distanceFromKm(?float $lat, ?float $lng): ?float
    {
        if ($lat === null || $lng === null || $this->latitude === null || $this->longitude === null) {
            return null;
        }

        $earthRadiusKm = 6371;

        $dLat = deg2rad($lat - $this->latitude);
        $dLng = deg2rad($lng - $this->longitude);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($this->latitude)) * cos(deg2rad($lat)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadiusKm * $c;
    }

    /**
     * Delivery fee for the given coordinates: distance (km) * rate per km.
     * Falls back to the flat delivery_fee column when location is unknown.
     */
    public function deliveryFeeFor(?float $lat, ?float $lng): float
    {
        $distance = $this->distanceFromKm($lat, $lng);

        return $distance !== null
            ? round($distance * self::DELIVERY_RATE_PER_KM, 2)
            : (float) $this->delivery_fee;
    }

    public function isWithinDeliveryRadius(?float $lat, ?float $lng): bool
    {
        $distance = $this->distanceFromKm($lat, $lng);

        return $distance === null || $distance <= self::VISIBILITY_RADIUS_KM;
    }

    /**
     * Whether the restaurant is open right now: a manual toggle (forced open or
     * forced closed) always wins over the opening/closing hours.
     */
    public function isCurrentlyOpen(): bool
    {
        if ($this->is_manually_closed) {
            return false;
        }

        if ($this->is_manually_opened) {
            return true;
        }

        if (! $this->opening_time || ! $this->closing_time) {
            return true;
        }

        // Restaurant hours are entered in Bangladesh local time, regardless of the app's UTC clock.
        $now = now('Asia/Dhaka')->format('H:i:s');
        $opens = $this->opening_time;
        $closes = $this->closing_time;

        if ($opens <= $closes) {
            return $now >= $opens && $now <= $closes;
        }

        // Overnight hours, e.g. 18:00–02:00.
        return $now >= $opens || $now <= $closes;
    }
}
