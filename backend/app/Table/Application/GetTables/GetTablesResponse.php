<?php

namespace App\Table\Application\GetTables;

use App\Table\Application\GetTable\GetTableResponse;

final readonly class GetTablesResponse
{
    /**
     * @param  GetTableResponse[]  $tables
     */
    public function __construct(
        public array $tables,
    ) {}

    /**
     * @param  GetTableResponse[]  $tables
     */
    public static function create(array $tables): self
    {
        return new self($tables);
    }

    /**
     * @return array<string, array<int, array<string, string>>>
     */
    public function toArray(): array
    {
        return [
            'tables' => array_map(
                fn (GetTableResponse $table) => $table->toArray(),
                $this->tables,
            ),
        ];
    }
}
