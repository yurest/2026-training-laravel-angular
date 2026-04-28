<?php

namespace App\Table\Application\GetTables;

use App\Table\Application\GetTable\GetTableResponse;
use App\Table\Domain\Interfaces\TableRepositoryInterface;

final class GetTables
{
    public function __construct(
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(): GetTablesResponse
    {
        $tables = $this->tableRepository->findAll();

        $tableResponses = array_map(
            fn ($table) => GetTableResponse::create($table),
            $tables,
        );

        return GetTablesResponse::create($tableResponses);
    }
}
