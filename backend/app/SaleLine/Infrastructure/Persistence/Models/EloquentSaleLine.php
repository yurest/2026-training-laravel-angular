<?php

namespace App\SaleLine\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentSaleLine extends Model
{
    use SoftDeletes;

    protected $table = 'sales_lines';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'sale_id',
        'order_line_id',
        'user_id',
        'quantity',
        'price',
        'tax_percentage',
    ];
}