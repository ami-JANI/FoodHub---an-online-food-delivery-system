<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantUpdateRequest extends Model
{
    protected $fillable = [
        'restaurant_id', 'name', 'cuisine', 'description', 'address_line',
        'latitude', 'longitude', 'logo', 'cover_image', 'status', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'reviewed_at' => 'datetime',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
