<?php

namespace App\Table\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentTable extends Model
{
    use SoftDeletes;

    protected $table = 'tables';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'zone_id',
        'name',
    ];

    public function getKeyName(): string
    {
        return 'id';
    }
}
