<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'order_id', 'restaurant_id', 'user_id', 'rating', 'body', 'is_anonymous', 'photos',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'photos' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reviewerName(): string
    {
        return $this->is_anonymous ? 'Anonymous' : $this->user->name;
    }
}
