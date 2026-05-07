<?php

namespace App\Zone\Application\GetZone;

final readonly class GetZoneCommand
{
    public function __construct(
        public string $id,
    ) {}
}
