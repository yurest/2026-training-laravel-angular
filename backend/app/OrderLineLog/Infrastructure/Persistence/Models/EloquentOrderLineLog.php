<?php

namespace App\OrderLineLog\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;

class EloquentOrderLineLog extends Model
{
    protected $table = 'order_line_logs';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'order_id',
        'order_line_id',
        'user_id',
        'action',
        'old_quantity',
        'new_quantity',
        'old_price',
        'new_price',
        'reason',
    ];
}