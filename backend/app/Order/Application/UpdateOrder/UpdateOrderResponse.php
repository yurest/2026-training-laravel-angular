<?php

namespace App\Order\Application\UpdateOrder;

use App\Order\Domain\Entity\Order;

final readonly class UpdateOrderResponse
{
    public function __construct(
        public string $id,
        public string $restaurantId,
        public string $status,
        public string $tableId,
        public string $openedByUserId,
        public ?string $closedByUserId,
        public int $diners,
        public string $openedAt,
        public ?string $closedAt,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function create(Order $order): self
    {
        return new self(
            id: $order->id()->value(),
            restaurantId: $order->restaurantId()->value(),
            status: $order->status()->value(),
            tableId: $order->tableId()->value(),
            openedByUserId: $order->openedByUserId()->value(),
            closedByUserId: $order->closedByUserId()?->value(),
            diners: $order->diners()->value(),
            openedAt: $order->openedAt()->format(\DateTimeInterface::ATOM),
            closedAt: $order->closedAt()?->format(\DateTimeInterface::ATOM),
            createdAt: $order->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $order->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurantId,
            'status' => $this->status,
            'table_id' => $this->tableId,
            'opened_by_user_id' => $this->openedByUserId,
            'closed_by_user_id' => $this->closedByUserId,
            'diners' => $this->diners,
            'opened_at' => $this->openedAt,
            'closed_at' => $this->closedAt,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}