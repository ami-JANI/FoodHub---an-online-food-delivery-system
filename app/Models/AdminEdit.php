<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminEdit extends Model
{
    protected $fillable = ['restaurant_id', 'summary', 'seen_by_restaurant'];

    protected function casts(): array
    {
        return [
            'seen_by_restaurant' => 'boolean',
        ];
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
