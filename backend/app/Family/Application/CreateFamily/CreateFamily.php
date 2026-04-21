<?php

namespace App\Family\Application\CreateFamily;

use App\Family\Domain\Entity\Family;
use App\Family\Domain\Interfaces\FamilyRepositoryInterface;
use App\Family\Domain\ValueObject\FamilyName;
use App\Shared\Domain\ValueObject\RestaurantId;

class CreateFamily
{
    public function __construct(
        private FamilyRepositoryInterface $familyRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $name,
    ): CreateFamilyResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $nameVO = FamilyName::create($name);
        $family = Family::dddCreate(
            $restaurantIdVO,
            $nameVO,
        );

        $this->familyRepository->save($family);

        return CreateFamilyResponse::create($family);
    }
}
