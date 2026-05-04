<?php

namespace App\OrderLine\Application\DeleteOrderLine;

use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;

final class DeleteOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $orderLine = $this->orderLineRepository->findById($id);

        if ($orderLine === null) {
            throw OrderLineNotFoundException::withId($id);
        }

        $this->orderLineRepository->delete($orderLine);
    }
}