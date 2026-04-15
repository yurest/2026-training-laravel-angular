<?php

namespace App\User\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EloquentUser extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'uuid',
        'restaurant_id',
        'role',
        'image_src',
        'name',
        'email',
        'password',
        'pin',
    ];

    protected $hidden = [
        'password',
        'pin',
    ];

    public function getKeyName(): string
    {
        return 'id';
    }
}