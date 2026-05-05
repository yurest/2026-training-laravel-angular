<?php

namespace App\Sale\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentSale extends Model
{
    use SoftDeletes;

    protected $table = 'sales';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'order_id',
        'user_id',
        'ticket_number',
        'value_date',
        'total',
    ];

    protected $casts = [
        'value_date' => 'datetime',
    ];
}