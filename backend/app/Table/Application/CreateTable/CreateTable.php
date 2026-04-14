<?php

namespace App\Table\Application\CreateTable;

use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\ZoneId;
use App\Table\Domain\Entity\Table;
use App\Table\Domain\Interfaces\TableRepositoryInterface;
use App\Table\Domain\ValueObject\TableName;

final class CreateTable
{
    public function __construct(
        private TableRepositoryInterface $tableRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $zoneId,
        string $name,
    ): CreateTableResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $zoneIdVO = ZoneId::create($zoneId);
        $nameVO = TableName::create($name);

        $table = Table::dddCreate(
            $restaurantIdVO,
            $zoneIdVO,
            $nameVO,
        );

        $this->tableRepository->save($table);

        return CreateTableResponse::create($table);
    }
}