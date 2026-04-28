<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $restaurantId = DB::table('restaurants')->first()->id;

        $beveragesId = DB::table('families')->where('name', 'Beverages')->first()->id;
        $appetizersId = DB::table('families')->where('name', 'Appetizers')->first()->id;
        $mainCoursesId = DB::table('families')->where('name', 'Main Courses')->first()->id;

        $tax21Id = DB::table('taxes')->where('percentage', 21)->first()->id;

        DB::table('products')->insert([
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'family_id' => $beveragesId,
                'tax_id' => $tax21Id,
                'name' => 'Coca-Cola',
                'price' => 350,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'family_id' => $beveragesId,
                'tax_id' => $tax21Id,
                'name' => 'Water',
                'price' => 200,
                'stock' => 200,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'family_id' => $appetizersId,
                'tax_id' => $tax21Id,
                'name' => 'Fries',
                'price' => 400,
                'stock' => 80,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid()->toString(),
                'restaurant_id' => $restaurantId,
                'family_id' => $mainCoursesId,
                'tax_id' => $tax21Id,
                'name' => 'Burger',
                'price' => 950,
                'stock' => 60,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
