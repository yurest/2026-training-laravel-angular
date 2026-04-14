<?php

namespace App\Restaurant\Application\GetRestaurants;

use App\Restaurant\Application\GetRestaurant\GetRestaurantResponse;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class GetRestaurants
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(): GetRestaurantsResponse
    {
        $restaurants = $this->restaurantRepository->findAll();

        $restaurantResponses = array_map(
            fn ($restaurant) => GetRestaurantResponse::create($restaurant),
            $restaurants,
        );

        return GetRestaurantsResponse::create($restaurantResponses);
    }
}
