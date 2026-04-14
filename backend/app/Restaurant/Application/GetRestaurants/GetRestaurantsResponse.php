<?php

namespace App\Restaurant\Application\GetRestaurants;

use App\Restaurant\Application\GetRestaurant\GetRestaurantResponse;

final readonly class GetRestaurantsResponse
{
    /**
     * @param array<int, GetRestaurantResponse> $restaurants
     */
    public function __construct(
        public array $restaurants,
    ) {}

    /**
     * @param array<int, GetRestaurantResponse> $restaurants
     */
    public static function create(array $restaurants): self
    {
        return new self($restaurants);
    }

    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public function toArray(): array
    {
        return [
            'restaurants' => array_map(
                fn (GetRestaurantResponse $restaurant) => $restaurant->toArray(),
                $this->restaurants,
            ),
        ];
    }
}
