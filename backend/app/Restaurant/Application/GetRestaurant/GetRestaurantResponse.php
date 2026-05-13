<?php

namespace App\Restaurant\Application\GetRestaurant;

use App\Restaurant\Domain\Entity\Restaurant;

final class GetRestaurantResponse
{
    private function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $legal_name,
        public readonly ?string $tax_id,
        public readonly string $email,
    ) {}

    public static function create(
        string $id,
        string $name,
        ?string $legal_name,
        ?string $tax_id,
        string $email,
    ): self {
        return new self(
            id: $id,
            name: $name,
            legal_name: $legal_name,
            tax_id: $tax_id,
            email: $email,
        );
    }

    public static function fromRestaurant(Restaurant $restaurant): self
    {
        return new self(
            id: $restaurant->id()->value(),
            name: $restaurant->name()->value(),
            legal_name: $restaurant->legalName()?->value(),
            tax_id: $restaurant->taxId()?->value(),
            email: $restaurant->email()->value(),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'tax_id' => $this->tax_id,
            'email' => $this->email,
        ];
    }
}
