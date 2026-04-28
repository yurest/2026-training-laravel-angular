<?php

namespace App\Family\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentFamily extends Model
{
    use SoftDeletes;

    protected $table = 'families';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'name',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getKeyName(): string
    {
        return 'id';
    }
}
