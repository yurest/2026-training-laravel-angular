<?php

namespace App\Restaurant\Application\RegisterRestaurantWithAdmin;

final readonly class RegisterRestaurantWithAdminCommand
{
    public function __construct(
        public string $restaurantName,
        public ?string $legalName,
        public ?string $taxId,
        public string $email,
        public string $plainPassword,
        public ?string $adminName,
        public ?string $adminPin,
    ) {}
}
