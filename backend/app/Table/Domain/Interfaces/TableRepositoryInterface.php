<?php

namespace App\Table\Domain\Interfaces;

use App\Table\Domain\Entity\Table;

interface TableRepositoryInterface
{
    public function save(Table $table): void;

    public function findById(string $id): ?Table;

    /**
     * @return array<int, Table>
     */
    public function findAll(): array;

    public function delete(Table $table): void;
}