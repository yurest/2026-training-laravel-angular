<?php

namespace App\Order\Application\GetOrders;

use App\Order\Application\GetOrder\GetOrderResponse;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;

final class GetOrders
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(): GetOrdersResponse
    {
        $orders = $this->orderRepository->findAll();

        $response = array_map(
            fn ($order) => GetOrderResponse::create($order),
            $orders,
        );

        return GetOrdersResponse::create($response);
    }
}