<?php

namespace App\Zone\Application\DeleteZone;

use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

final class DeleteZone
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $zone = $this->zoneRepository->findById($id);

        if ($zone === null) {
            throw ZoneNotFoundException::withId($id);
        }

        $this->zoneRepository->delete($zone);
    }
}