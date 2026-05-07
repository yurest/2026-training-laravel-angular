<?php

namespace App\User\Application\AuthorizeRestaurantAccess;

final readonly class AuthorizeRestaurantAccessResponse
{
    private function __construct() {}

    public static function create(): self
    {
        return new self;
    }

    public function toArray(): array
    {
        return [];
    }
}
