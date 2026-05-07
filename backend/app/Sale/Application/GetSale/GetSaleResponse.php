<?php

namespace App\Sale\Application\GetSale;

use App\Sale\Domain\Entity\Sale;

final readonly class GetSaleResponse
{
    public function __construct(
        public string $id,
        public string $restaurantId,
        public string $orderId,
        public string $userId,
        public ?int $ticketNumber,
        public string $valueDate,
        public int $total,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function create(Sale $sale): self
    {
        return new self(
            id: $sale->id()->value(),
            restaurantId: $sale->restaurantId()->value(),
            orderId: $sale->orderId()->value(),
            userId: $sale->userId()->value(),
            ticketNumber: $sale->ticketNumber()?->value(),
            valueDate: $sale->valueDate()->format(\DateTimeInterface::ATOM),
            total: $sale->total()->value(),
            createdAt: $sale->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $sale->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @return array<string, string|int|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurantId,
            'order_id' => $this->orderId,
            'user_id' => $this->userId,
            'ticket_number' => $this->ticketNumber,
            'value_date' => $this->valueDate,
            'total' => $this->total,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}