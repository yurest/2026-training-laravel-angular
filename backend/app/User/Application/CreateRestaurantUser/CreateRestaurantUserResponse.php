<?php

namespace App\User\Application\CreateRestaurantUser;

final readonly class CreateRestaurantUserResponse
{
    private function __construct(
        public string $uuid,
        public string $name,
        public string $email,
        public string $role,
    ) {}

    public static function create(
        string $uuid,
        string $name,
        string $email,
        string $role,
    ): self {
        return new self(
            uuid: $uuid,
            name: $name,
            email: $email,
            role: $role,
        );
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
        ];
    }
}
