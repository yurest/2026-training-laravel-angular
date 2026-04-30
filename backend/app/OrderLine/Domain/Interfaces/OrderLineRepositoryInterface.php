<?php

namespace App\OrderLine\Domain\Interfaces;

use App\OrderLine\Domain\Entity\OrderLine;

interface OrderLineRepositoryInterface
{
    public function save(OrderLine $orderLine): void;

    public function findById(string $id): ?OrderLine;

    /**
     * @return array<int, OrderLine>
     */
    public function findAll(): array;

    public function delete(OrderLine $orderLine): void;
}
