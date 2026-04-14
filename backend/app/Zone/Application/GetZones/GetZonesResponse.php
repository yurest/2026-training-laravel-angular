<?php

namespace App\Zone\Application\GetZones;

use App\Zone\Application\GetZone\GetZoneResponse;

final readonly class GetZonesResponse
{
    /**
     * @param GetZoneResponse[] $zones
     */
    public function __construct(
        public array $zones,
    ) {}

    /**
     * @param GetZoneResponse[] $zones
     */
    public static function create(array $zones): self
    {
        return new self($zones);
    }

    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public function toArray(): array
    {
        return [
            'zones' => array_map(
                fn (GetZoneResponse $zone) => $zone->toArray(),
                $this->zones,
            ),
        ];
    }
}