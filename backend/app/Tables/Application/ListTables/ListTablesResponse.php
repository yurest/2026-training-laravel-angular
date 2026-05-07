<?php

namespace App\Tables\Application\ListTables;

final readonly class ListTablesResponse
{
    /**
     * @param  array<int, array<string, string>>  $items
     */
    private function __construct(
        public array $items,
    ) {}

    /**
     * @param  array<int, array<string, string>>  $items
     */
    public static function create(array $items): self
    {
        return new self(
            items: $items,
        );
    }

    /**
     * @return array<int, array<string, string>>
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
