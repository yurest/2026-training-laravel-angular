<?php

namespace App\Order\Domain\Interfaces;

use App\Order\Domain\Entity\Order;

interface OrderRepositoryInterface
{
    public function save(Order $order): void;

    public function findById(string $id): ?Order;

    /**
     * @return Order[]
     */
    public function findAll(): array;
    public function delete(Order $order): void;
}