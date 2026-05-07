<?php

namespace App\OrderLine\Application\GetOrderLine;

use App\OrderLine\Domain\Entity\OrderLine;

final readonly class GetOrderLineResponse
{
    public function __construct(
        public string $id,
        public string $restaurantId,
        public string $orderId,
        public string $productId,
        public string $userId,
        public int $quantity,
        public int $price,
        public int $taxPercentage,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function create(OrderLine $orderLine): self
    {
        return new self(
            id: $orderLine->id()->value(),
            restaurantId: $orderLine->restaurantId()->value(),
            orderId: $orderLine->orderId()->value(),
            productId: $orderLine->productId()->value(),
            userId: $orderLine->userId()->value(),
            quantity: $orderLine->quantity()->value(),
            price: $orderLine->price()->value(),
            taxPercentage: $orderLine->taxPercentage()->value(),
            createdAt: $orderLine->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $orderLine->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurantId,
            'order_id' => $this->orderId,
            'product_id' => $this->productId,
            'user_id' => $this->userId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'tax_percentage' => $this->taxPercentage,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}