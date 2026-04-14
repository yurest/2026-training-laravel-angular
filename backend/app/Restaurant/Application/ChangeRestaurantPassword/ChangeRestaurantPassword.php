<?php

namespace App\Restaurant\Application\ChangeRestaurantPassword;

use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;
use App\Restaurant\Domain\ValueObject\RestaurantPasswordHash;
use App\Shared\Domain\Interfaces\PasswordHasherInterface;

final class ChangeRestaurantPassword
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(string $id, string $plainPassword): ChangeRestaurantPasswordResponse
    {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            throw RestaurantNotFoundException::withId($id);
        }

        $passwordHash = RestaurantPasswordHash::create(
            $this->passwordHasher->hash($plainPassword),
        );

        $restaurant = $restaurant->changePassword($passwordHash);

        $this->restaurantRepository->save($restaurant);

        return ChangeRestaurantPasswordResponse::create($restaurant);
    }
}
