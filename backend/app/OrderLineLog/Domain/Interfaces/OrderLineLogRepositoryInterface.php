<?php

namespace App\OrderLineLog\Domain\Interfaces;

use App\OrderLineLog\Domain\Entity\OrderLineLog;

interface OrderLineLogRepositoryInterface
{
    public function save(OrderLineLog $orderLineLog): void;
}