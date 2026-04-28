<?php

namespace App\User\Infrastructure\Persistence\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class EloquentUser extends Authenticatable
{
    use HasApiTokens;
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
