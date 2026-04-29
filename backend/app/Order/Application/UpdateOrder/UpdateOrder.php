<?php

namespace App\Order\Application\UpdateOrder;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\Diners;
use App\Order\Domain\ValueObject\OrderStatus;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\TableId;
use App\Shared\Domain\ValueObject\UserId;

final class UpdateOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
    ) {}

    public function __invoke(
        string $id,
        ?string $status = null,
        ?string $tableId = null,
        ?string $closedByUserId = null,
        ?int $diners = null,
        ?string $closedAt = null,
    ): UpdateOrderResponse {
        $order = $this->orderRepository->findById($id);

        if ($order === null) {
            throw OrderNotFoundException::withId($id);
        }

        $statusVO = $status !== null
            ? OrderStatus::create($status)
            : $order->status();

        $tableIdVO = $tableId !== null
            ? TableId::create($tableId)
            : $order->tableId();

        $closedByUserIdVO = $closedByUserId !== null
            ? UserId::create($closedByUserId)
            : $order->closedByUserId();

        $dinersVO = $diners !== null
            ? Diners::create($diners)
            : $order->diners();

        $closedAtVO = $closedAt !== null
            ? DomainDateTime::create(new \DateTimeImmutable($closedAt))
            : $order->closedAt();

        $order = $order->update(
            $statusVO,
            $tableIdVO,
            $closedByUserIdVO,
            $dinersVO,
            $closedAtVO,
        );

        $this->orderRepository->save($order);

        return UpdateOrderResponse::create($order);
    }
}