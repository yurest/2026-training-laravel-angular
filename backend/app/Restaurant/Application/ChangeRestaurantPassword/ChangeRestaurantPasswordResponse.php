<?php

namespace App\Restaurant\Application\ChangeRestaurantPassword;

use App\Restaurant\Domain\Entity\Restaurant;

final readonly class ChangeRestaurantPasswordResponse
{
    public function __construct(
        public string $id,
        public string $updatedAt,
    ) {}

    public static function create(Restaurant $restaurant): self
    {
        return new self(
            id: $restaurant->id()->value(),
            updatedAt: $restaurant->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'updated_at' => $this->updatedAt,
        ];
    }
}
