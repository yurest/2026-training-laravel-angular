<?php

namespace App\Table\Application\DeleteTable;

use App\Table\Domain\Exception\TableNotFoundException;
use App\Table\Domain\Interfaces\TableRepositoryInterface;

final class DeleteTable
{
    public function __construct(
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $table = $this->tableRepository->findById($id);

        if ($table === null) {
            throw TableNotFoundException::withId($id);
        }

        $this->tableRepository->delete($table);
    }
}
