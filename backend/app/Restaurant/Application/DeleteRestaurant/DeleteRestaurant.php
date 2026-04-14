<?php

namespace App\Restaurant\Application\DeleteRestaurant;

use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class DeleteRestaurant
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            throw RestaurantNotFoundException::withId($id);
        }

        $this->restaurantRepository->delete($restaurant);
    }
}
