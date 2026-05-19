<?php

namespace App\OrderLineLog\Application\CreateOrderLineLog;

use App\OrderLineLog\Domain\Entity\OrderLineLog;
use App\OrderLineLog\Domain\Interfaces\OrderLineLogRepositoryInterface;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\UserId;

final class CreateOrderLineLog
{
    public function __construct(
        private OrderLineLogRepositoryInterface $orderLineLogRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $orderId,
        ?string $orderLineId,
        string $userId,
        string $action,
        ?int $oldQuantity,
        ?int $newQuantity,
        ?int $oldPrice,
        ?int $newPrice,
        ?string $reason = null,
    ): void { 
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $orderIdVO = OrderId::create($orderId);
        $userIdVO = UserId::create($userId);

        $log = OrderLineLog::dddCreate(
            $restaurantIdVO,
            $orderIdVO,
            $orderLineId,
            $userIdVO,
            $action,
            $oldQuantity,
            $newQuantity,
            $oldPrice,
            $newPrice,
            $reason,
        );

        $this->orderLineLogRepository->save($log);
    }
}