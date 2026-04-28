<?php

namespace App\Table\Application\UpdateTable;

use App\Shared\Domain\ValueObject\ZoneId;
use App\Table\Domain\Exception\TableNotFoundException;
use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\ValueObject\TableName;

final class UpdateTable
{
    public function __construct(
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(
        string $id,
        ?string $zoneId = null,
        ?string $name = null,
    ): UpdateTableResponse {
        $table = $this->tableRepository->findById($id);

        if ($table === null) {
            throw TableNotFoundException::withId($id);
        }

        $zoneIdVO = $zoneId !== null
            ? ZoneId::create($zoneId)
            : $table->zoneId();

        $nameVO = $name !== null
            ? TableName::create($name)
            : $table->name();

        $table = $table->update(
            $zoneIdVO,
            $nameVO,
        );

        $this->tableRepository->save($table);

        return UpdateTableResponse::create($table);
    }
}
