<?php

namespace App\Restaurant\Application\DeleteRestaurant;

use App\Restaurant\Domain\Exception\NotSuperAdminException;
use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Domain\Interfaces\RestaurantCascadeDeleteInterface;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class DeleteRestaurant
{
    public function __construct(
        private readonly RestaurantRepositoryInterface $restaurantRepository,
        private readonly RestaurantCascadeDeleteInterface $restaurantCascadeDelete,
    ) {}

    public function __invoke(DeleteRestaurantCommand $command): void
    {
        if (! is_string($command->superAdminUuid) || $command->superAdminUuid === '') {
            throw NotSuperAdminException::create();
        }

        $restaurant = $this->restaurantRepository->getById($command->id);

        if ($restaurant === null) {
            throw RestaurantNotFoundException::withUuid($command->id);
        }

        $this->restaurantCascadeDelete->deleteByRestaurantUuid($command->id);
    }
}
