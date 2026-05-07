<?php

namespace App\Family\Application\ListFamilies;

final readonly class ListFamiliesResponse
{
    /**
     * @param  array<int, array<string, bool|string>>  $items
     */
    private function __construct(
        public array $items,
    ) {}

    /**
     * @param  array<int, array<string, bool|string>>  $items
     */
    public static function create(array $items): self
    {
        return new self(
            items: $items,
        );
    }

    /**
     * @return array<int, array<string, bool|string>>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
