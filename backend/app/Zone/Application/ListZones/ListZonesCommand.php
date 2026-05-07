<?php

namespace App\Zone\Application\ListZones;

final readonly class ListZonesCommand
{
    public function __construct(
        public ?bool $includeDeleted,
    ) {}
}
