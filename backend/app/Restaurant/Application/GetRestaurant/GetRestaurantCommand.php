<?php

namespace App\Restaurant\Application\GetRestaurant;

final readonly class GetRestaurantCommand
{
    public function __construct(
        public string $id,
    ) {}
}
