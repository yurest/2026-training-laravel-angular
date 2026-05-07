<?php

namespace App\User\Application\UpdateRestaurantUser;

final readonly class UpdateRestaurantUserResponse
{
    private function __construct(
        public string $uuid,
    ) {}

    public static function create(string $uuid): self
    {
        return new self(
            uuid: $uuid,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
        ];
    }
}
