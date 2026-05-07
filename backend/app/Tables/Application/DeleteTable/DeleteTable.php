<?php

namespace App\Tables\Application\DeleteTable;

use App\Tables\Domain\Exception\TableNotFoundException;
use App\Tables\Domain\Interfaces\TableRepositoryInterface;

class DeleteTable
{
    public function __construct(
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(DeleteTableCommand $command): void
    {
        $table = $this->tableRepository->findById($command->id)
            ?? throw TableNotFoundException::withId($command->id);

        $this->tableRepository->deleteById($table->id()->value());
    }
}
