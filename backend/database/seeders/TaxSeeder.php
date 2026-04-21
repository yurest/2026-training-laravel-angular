<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TaxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $restaurantId = DB::table('restaurants')->first()->id;

        DB::table('taxes')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'VAT',
                'percentage' => 21,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Reduced VAT',
                'percentage' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'name' => 'Super Reduced VAT',
                'percentage' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
