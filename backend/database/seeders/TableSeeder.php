<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantId = DB::table('restaurants')->first()->id;
        $terraceId = DB::table('zones')->where('name', 'Terraza')->first()->id;
        $diningRoomId = DB::table('zones')->where('name', 'Salón')->first()->id;
        $barId = DB::table('zones')->where('name', 'Barra')->first()->id;
        DB::table('tables')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $terraceId,
                'name' => 'Mesa 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $terraceId,
                'name' => 'Mesa 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $diningRoomId,
                'name' => 'Mesa 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $diningRoomId,
                'name' => 'Mesa 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $diningRoomId,
                'name' => 'Mesa 5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $barId,
                'name' => 'Mesa 6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $barId,
                'name' => 'Mesa 7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
