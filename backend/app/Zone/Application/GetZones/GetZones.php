<?php

namespace App\Zone\Application\GetZones;

use App\Zone\Application\GetZone\GetZoneResponse;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

final class GetZones
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(): GetZonesResponse
    {
        $zones = $this->zoneRepository->findAll();

        $zoneResponses = array_map(
            fn ($zone) => GetZoneResponse::create($zone),
            $zones,
        );

        return GetZonesResponse::create($zoneResponses);
    }
}
