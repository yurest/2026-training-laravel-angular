<?php

namespace App\User\Application\GetMe;

final readonly class GetMeResponse
{
    private function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $role,
        public ?string $restaurantId,
        public ?string $restaurantName,
    ) {}

    public static function create(
        string $id,
        string $name,
        string $email,
        ?string $role,
        ?string $restaurantId,
        ?string $restaurantName,
    ): self {
        return new self(
            id: $id,
            name: $name,
            email: $email,
            role: $role,
            restaurantId: $restaurantId,
            restaurantName: $restaurantName,
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'restaurant_id' => $this->restaurantId,
            'restaurant_name' => $this->restaurantName,
        ];
    }
}
