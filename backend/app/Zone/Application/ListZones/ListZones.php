<?php

namespace App\Zone\Application\ListZones;

use App\Zone\Domain\Entity\Zone;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;

class ListZones
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(ListZonesCommand $command): ListZonesResponse
    {
        $zones = $this->zoneRepository->findAll($command->includeDeleted ?? false);

        $items = array_map(
            static fn (Zone $zone): array => ListZonesItemResponse::create(
                id: $zone->id()->value(),
                name: $zone->name()->value(),
                createdAt: $zone->createdAt()->format(\DateTimeInterface::ATOM),
                updatedAt: $zone->updatedAt()->format(\DateTimeInterface::ATOM),
            )->toArray(),
            $zones,
        );

        return ListZonesResponse::create(
            items: $items,
        );
    }
}
