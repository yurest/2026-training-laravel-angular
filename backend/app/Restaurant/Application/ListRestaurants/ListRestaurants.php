<?php

namespace App\Restaurant\Application\ListRestaurants;

use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class ListRestaurants
{
    public function __construct(
        private readonly RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(): ListRestaurantsCollectionResponse
    {
        $restaurants = $this->restaurantRepository->all();

        return ListRestaurantsCollectionResponse::create($restaurants);
    }
}
