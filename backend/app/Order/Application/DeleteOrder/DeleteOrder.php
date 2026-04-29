<?php

namespace App\Order\Application\DeleteOrder;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;

final class DeleteOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $order = $this->orderRepository->findById($id);

        if ($order === null) {
            throw OrderNotFoundException::withId($id);
        }

        $this->orderRepository->delete($order);
    }
}