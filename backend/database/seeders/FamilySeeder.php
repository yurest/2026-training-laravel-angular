<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantId = DB::table('restaurants')->first()->id;

        DB::table('families')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Refrescos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Bebidas Alcohólicas',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Cafés',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Entrantes',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Platos Principales',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
