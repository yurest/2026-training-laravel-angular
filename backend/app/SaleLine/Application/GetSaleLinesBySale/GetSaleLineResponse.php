<?php

namespace App\SaleLine\Application\GetSaleLinesBySale;

use App\SaleLine\Domain\Entity\SaleLine;

final readonly class GetSaleLineResponse
{
    public function __construct(
        public string $id,
        public string $restaurantId,
        public string $saleId,
        public string $orderLineId,
        public string $userId,
        public int $quantity,
        public int $price,
        public int $taxPercentage,
        public string $createdAt,
        public string $updatedAt,
    ) {}

    public static function create(SaleLine $saleLine): self
    {
        return new self(
            id: $saleLine->id()->value(),
            restaurantId: $saleLine->restaurantId()->value(),
            saleId: $saleLine->saleId()->value(),
            orderLineId: $saleLine->orderLineId()->value(),
            userId: $saleLine->userId()->value(),
            quantity: $saleLine->quantity()->value(),
            price: $saleLine->price()->value(),
            taxPercentage: $saleLine->taxPercentage()->value(),
            createdAt: $saleLine->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $saleLine->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }

    /**
     * @return array<string, string|int>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'restaurant_id' => $this->restaurantId,
            'sale_id' => $this->saleId,
            'order_line_id' => $this->orderLineId,
            'user_id' => $this->userId,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'tax_percentage' => $this->taxPercentage,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
        ];
    }
}
