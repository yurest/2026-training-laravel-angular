<?php

namespace App\OrderLine\Application\DeleteOrderLine;

use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\OrderLineLog\Application\CreateOrderLineLog\CreateOrderLineLog;

final class DeleteOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
        private CreateOrderLineLog $createOrderLineLog,
    ) {}

    public function __invoke(string $id): void
    {
        $orderLine = $this->orderLineRepository->findById($id);

        if ($orderLine === null) {
            throw OrderLineNotFoundException::withId($id);
        }

          // LOG
        ($this->createOrderLineLog)(
            $orderLine->restaurantId()->value(),
            $orderLine->orderId()->value(),
            $orderLine->id()->value(),
            $orderLine->userId()->value(),
            'deleted',
            $orderLine->quantity()->value(),
            null,
            $orderLine->price()->value(),
            null,
            null,
        );

        $this->orderLineRepository->delete($orderLine);
    }
}