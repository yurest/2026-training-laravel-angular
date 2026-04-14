<?php

namespace App\Restaurant\Application\UpdateRestaurant;

use App\Restaurant\Domain\Exception\RestaurantNotFoundException;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;
use App\Restaurant\Domain\ValueObject\RestaurantLegalName;
use App\Restaurant\Domain\ValueObject\RestaurantName;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\TaxId;

final class UpdateRestaurant
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(
        string $id,
        ?string $name = null,
        ?string $legalName = null,
        ?string $taxId = null,
        ?string $email = null,
    ): UpdateRestaurantResponse {
        $restaurant = $this->restaurantRepository->findById($id);

        if ($restaurant === null) {
            throw RestaurantNotFoundException::withId($id);
        }

        $nameVO = $name !== null
            ? RestaurantName::create($name)
            : $restaurant->name();

        $legalNameVO = $legalName !== null
            ? RestaurantLegalName::create($legalName)
            : $restaurant->legalName();

        $taxIdVO = $taxId !== null
            ? TaxId::create($taxId)
            : $restaurant->taxId();

        $emailVO = $email !== null
            ? Email::create($email)
            : $restaurant->email();

        $restaurant = $restaurant->update(
            $nameVO,
            $legalNameVO,
            $taxIdVO,
            $emailVO,
        );

        $this->restaurantRepository->save($restaurant);

        return UpdateRestaurantResponse::create($restaurant);
    }
}