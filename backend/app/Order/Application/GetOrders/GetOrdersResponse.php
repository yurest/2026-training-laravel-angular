<?php

namespace App\Order\Application\GetOrders;

use App\Order\Application\GetOrder\GetOrderResponse;

final readonly class GetOrdersResponse
{
    /**
     * @param GetOrderResponse[] $orders
     */
    public function __construct(
        public array $orders,
    ) {}

    /**
     * @param GetOrderResponse[] $orders
     */
    public static function create(array $orders): self
    {
        return new self($orders);
    }

    public function toArray(): array
    {
        return [
            'orders' => array_map(
                fn (GetOrderResponse $order) => $order->toArray(),
                $this->orders,
            ),
        ];
    }
}