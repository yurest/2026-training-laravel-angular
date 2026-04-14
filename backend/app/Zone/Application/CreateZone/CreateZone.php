<?php

namespace App\Zone\Application\CreateZone;

use App\Shared\Domain\ValueObject\RestaurantId;
use App\Zone\Domain\Entity\Zone;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;
use App\Zone\Domain\ValueObject\ZoneName;

final class CreateZone
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $name,
    ): CreateZoneResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $nameVO = ZoneName::create($name);

        $zone = Zone::dddCreate(
            $restaurantIdVO,
            $nameVO,
        );

        $this->zoneRepository->save($zone);

        return CreateZoneResponse::create($zone);
    }
}