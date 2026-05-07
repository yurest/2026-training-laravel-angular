<?php

namespace App\Zone\Application\GetZone;

use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

class GetZone
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(GetZoneCommand $command): GetZoneResponse
    {
        $zone = $this->zoneRepository->findById($command->id)
            ?? throw ZoneNotFoundException::withId($command->id);

        return GetZoneResponse::create(
            id: $zone->id()->value(),
            name: $zone->name()->value(),
            createdAt: $zone->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $zone->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
