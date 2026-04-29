<?php

namespace App\Order\Application\GetOrder;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;

final class GetOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(string $id): GetOrderResponse
    {
        $order = $this->orderRepository->findById($id);

        if ($order === null) {
            throw OrderNotFoundException::withId($id);
        }

        return GetOrderResponse::create($order);
    }
}