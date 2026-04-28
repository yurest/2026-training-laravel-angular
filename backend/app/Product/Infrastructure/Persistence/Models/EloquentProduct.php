<?php

namespace App\Product\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentProduct extends Model
{
    use SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'family_id',
        'tax_id',
        'stock',
        'image_src',
        'active',
        'name',
        'price',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getKeyName(): string
    {
        return 'id';
    }
}
