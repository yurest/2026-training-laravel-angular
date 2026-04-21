<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantId = DB::table('restaurants')->first()->id;

        DB::table('users')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'role' => 'admin',
                'image_src' => null,
                'name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('admin'),
                'pin' => '1234',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'role' => 'operator',
                'image_src' => null,
                'name' => 'user2',
                'email' => 'user2@example.com',
                'password' => Hash::make('user2'),
                'pin' => '2222',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'role' => 'operator',
                'image_src' => null,
                'name' => 'user3',
                'email' => 'user3@example.com',
                'password' => Hash::make('user3'),
                'pin' => '3333',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
