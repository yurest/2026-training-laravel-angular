<?php

namespace App\Zone\Application\GetZone;

use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

final class GetZone
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(string $id): GetZoneResponse
    {
        $zone = $this->zoneRepository->findById($id);

        if ($zone === null) {
            throw ZoneNotFoundException::withId($id);
        }

        return GetZoneResponse::create($zone);
    }
}