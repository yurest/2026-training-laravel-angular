<?php

namespace App\Order\Application\CreateOrder;

use App\Order\Domain\Entity\Order;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\Diners;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\TableId;
use App\Shared\Domain\ValueObject\UserId;

final class CreateOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $tableId,
        string $openedByUserId,
        int $diners,
    ): CreateOrderResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $tableIdVO = TableId::create($tableId);
        $openedByUserIdVO = UserId::create($openedByUserId);
        $dinersVO = Diners::create($diners);
        $order = Order::dddCreate(
            $restaurantIdVO,
            $tableIdVO,
            $openedByUserIdVO,
            $dinersVO,
        );

        $this->orderRepository->save($order);

        return CreateOrderResponse::create($order);
    }
}