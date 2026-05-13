<?php

namespace App\Restaurant\Infrastructure\Persistence\DTO;

use App\Restaurant\Domain\Entity\Restaurant;

final readonly class RestaurantWithInternalId
{
    public function __construct(
        public Restaurant $restaurant,
        public int $internalId,
    ) {}
}
