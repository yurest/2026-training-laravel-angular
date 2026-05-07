<?php

namespace App\Zone\Application\DeleteZone;

use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

class DeleteZone
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(DeleteZoneCommand $command): void
    {
        $zone = $this->zoneRepository->findById($command->id)
            ?? throw ZoneNotFoundException::withId($command->id);

        $this->zoneRepository->deleteById($zone->id()->value());
    }
}
