<?php

namespace App\Restaurant\Application\GetRestaurant;

use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class GetRestaurant
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(string $id): GetRestaurantResponse
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            throw RestaurantNotFoundException::withId($id);
        }

        return GetRestaurantResponse::create($restaurant);
    }
}
