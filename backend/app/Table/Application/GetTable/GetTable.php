<?php

namespace App\Table\Application\GetTable;

use App\Table\Domain\Exception\TableNotFoundException;
use App\Table\Domain\Interfaces\TableRepositoryInterface;

final class GetTable
{
    public function __construct(
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(string $id): GetTableResponse
    {
        $table = $this->tableRepository->findById($id);

        if ($table === null) {
            throw TableNotFoundException::withId($id);
        }

        return GetTableResponse::create($table);
    }
}