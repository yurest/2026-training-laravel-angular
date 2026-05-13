<?php

namespace App\Restaurant\Application\DeleteRestaurant;

final readonly class DeleteRestaurantCommand
{
    public function __construct(
        public string $id,
        public ?string $superAdminUuid,
    ) {}
}
