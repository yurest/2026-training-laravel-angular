<?php

namespace App\Order\Application\GetOpenOrders;

use App\Order\Application\GetOrder\GetOrderResponse;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;

final class GetOpenOrders
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(string $restaurantId): GetOpenOrdersResponse
    {
        $orders = $this->orderRepository->findOpenByRestaurant($restaurantId);

        $response = array_map(
            fn ($order) => GetOrderResponse::create($order),
            $orders,
        );

        return GetOpenOrdersResponse::create($response);
    }
}