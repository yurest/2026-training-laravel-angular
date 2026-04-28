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
        $terraceId = DB::table('zones')->where('name', 'Terrace')->first()->id;
        $diningRoomId = DB::table('zones')->where('name', 'Dining Room')->first()->id;
        $barId = DB::table('zones')->where('name', 'Bar')->first()->id;
        DB::table('tables')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $terraceId,
                'name' => 'Table 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $terraceId,
                'name' => 'Table 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $diningRoomId,
                'name' => 'Table 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $diningRoomId,
                'name' => 'Table 4',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $diningRoomId,
                'name' => 'Table 5',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $barId,
                'name' => 'Table 6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'zone_id' => $barId,
                'name' => 'Table 7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
