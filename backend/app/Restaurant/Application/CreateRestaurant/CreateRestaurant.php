<?php

namespace App\Restaurant\Application\CreateRestaurant;

use App\Restaurant\Domain\Entity\Restaurant;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;
use App\Restaurant\Domain\ValueObject\CompanyTaxId;
use App\Restaurant\Domain\ValueObject\RestaurantLegalName;
use App\Restaurant\Domain\ValueObject\RestaurantName;
use App\Restaurant\Domain\ValueObject\RestaurantPasswordHash;
use App\Shared\Domain\Interfaces\PasswordHasherInterface;
use App\Shared\Domain\ValueObject\Email;

final class CreateRestaurant
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
        private PasswordHasherInterface $passwordHasher,
    ) {}

    public function __invoke(
        string $name,
        string $legalName,
        string $taxId,
        string $email,
        string $plainPassword,
    ): CreateRestaurantResponse {
        $nameVO = RestaurantName::create($name);
        $legalNameVO = RestaurantLegalName::create($legalName);
        $fiscalIdVO = CompanyTaxId::create($taxId);
        $emailVO = Email::create($email);
        $passwordHashVO = RestaurantPasswordHash::create(
            $this->passwordHasher->hash($plainPassword),
        );

        $restaurant = Restaurant::dddCreate(
            $nameVO,
            $legalNameVO,
            $fiscalIdVO,
            $emailVO,
            $passwordHashVO,
        );

        $this->restaurantRepository->save($restaurant);

        return CreateRestaurantResponse::create($restaurant);
    }
}
