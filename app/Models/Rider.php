<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Rider extends Authenticatable
{
    use Notifiable;

    public const PICKUP_RADIUS_KM = 2.0;

    protected $fillable = [
        'name', 'phone', 'email', 'password',
        'educational_qualification', 'vehicle_type', 'hourly_wage', 'is_approved',
        'last_latitude', 'last_longitude', 'last_seen_at',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'hourly_wage' => 'float',
            'is_approved' => 'boolean',
            'last_latitude' => 'float',
            'last_longitude' => 'float',
            'last_seen_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }
}
