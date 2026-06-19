<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderMessage extends Model
{
    public const SENDER_CUSTOMER = 'customer';

    public const SENDER_RIDER = 'rider';

    protected $fillable = ['order_id', 'sender_type', 'body'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
