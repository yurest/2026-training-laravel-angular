<?php

namespace App\Zone\Application\UpdateZone;

use App\Zone\Domain\Exception\ZoneNotFoundException;
use App\Zone\Domain\Interfaces\ZoneRepositoryInterface;
use App\Zone\Domain\ValueObject\ZoneName;

final class UpdateZone
{
    public function __construct(
        private ZoneRepositoryInterface $zoneRepository,
    ) {}

    public function __invoke(
        string $id,
        ?string $name = null,
    ): UpdateZoneResponse {
        $zone = $this->zoneRepository->findById($id);

        if ($zone === null) {
            throw ZoneNotFoundException::withId($id);
        }

        $nameVO = $name !== null
            ? ZoneName::create($name)
            : $zone->name();

        $zone = $zone->update(
            $nameVO,
        );

        $this->zoneRepository->save($zone);

        return UpdateZoneResponse::create($zone);
    }
}