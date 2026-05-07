<?php

namespace App\User\Application\AuthenticateForDeviceLink;

final readonly class AuthenticateForDeviceLinkResponse
{
    private function __construct(
        public string $id,
        public string $name,
        public string $email,
        public ?string $restaurantId,
        public ?string $restaurantName,
    ) {}

    public static function create(
        string $id,
        string $name,
        string $email,
        ?string $restaurantId,
        ?string $restaurantName,
    ): self {
        return new self(
            id: $id,
            name: $name,
            email: $email,
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
            'restaurantId' => $this->restaurantId,
            'restaurantName' => $this->restaurantName,
        ];
    }
}
