<?php

namespace App\Restaurant\Application\ListRestaurants;

final readonly class ListRestaurantsCollectionResponse
{
    public function __construct(
        public array $data,
    ) {}

    public static function create(array $restaurants): self
    {
        return new self(
            data: array_map(
                static fn ($restaurant): array => ListRestaurantsResponse::fromRestaurant($restaurant)->toArray(),
                $restaurants,
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'data' => $this->data,
        ];
    }
}
