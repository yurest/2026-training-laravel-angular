<?php

namespace App\Restaurant\Application\GetRestaurant;

use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class GetRestaurant
{
    public function __construct(
        private readonly RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(GetRestaurantCommand $command): GetRestaurantResponse
    {
        $restaurant = $this->restaurantRepository->getById($command->id);

        if ($restaurant === null) {
            throw RestaurantNotFoundException::withUuid($command->id);
        }

        return GetRestaurantResponse::fromRestaurant($restaurant);
    }
}
