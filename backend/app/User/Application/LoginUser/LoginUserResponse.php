<?php

namespace App\User\Application\LoginUser;

use App\User\Domain\Entity\User;

final readonly class LoginUserResponse
{
    public function __construct(
        public string $token,
        public string $id,
        public string $restaurantId,
        public string $role,
        public ?string $imageSrc,
        public string $name,
        public string $email,
    ) {}

    public static function create(User $user, string $token): self
    {
        return new self(
            token: $token,
            id: $user->id()->value(),
            restaurantId: $user->restaurantId()->value(),
            role: $user->role()->value(),
            imageSrc: $user->imageSrc()->value(),
            name: $user->name()->value(),
            email: $user->email()->value(),
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'token' => $this->token,
            'user' => [
                'id' => $this->id,
                'restaurant_id' => $this->restaurantId,
                'role' => $this->role,
                'image_src' => $this->imageSrc,
                'name' => $this->name,
                'email' => $this->email,
            ],
        ];
    }
}
