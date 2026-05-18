<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantId = DB::table('restaurants')->first()->id;

        DB::table('zones')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Terraza',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Salón',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Barra',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
