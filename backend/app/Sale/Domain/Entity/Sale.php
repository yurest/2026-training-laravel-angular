<?php

namespace App\Sale\Domain\Entity;

use App\Sale\Domain\ValueObject\SaleTicketNumber;
use App\Sale\Domain\ValueObject\SaleTotal;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\UserId;
use App\Shared\Domain\ValueObject\Uuid;

class Sale
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private OrderId $orderId,
        private UserId $userId,
        private ?SaleTicketNumber $ticketNumber,
        private DomainDateTime $valueDate,
        private SaleTotal $total,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        OrderId $orderId,
        UserId $userId,
        DomainDateTime $valueDate,
        SaleTotal $total,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $orderId,
            $userId,
            null,
            $valueDate,
            $total,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $orderId,
        string $userId,
        ?int $ticketNumber,
        \DateTimeImmutable $valueDate,
        int $total,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            OrderId::create($orderId),
            UserId::create($userId),
            $ticketNumber !== null ? SaleTicketNumber::create($ticketNumber) : null,
            DomainDateTime::create($valueDate),
            SaleTotal::create($total),
            DomainDateTime::create($createdAt),
            DomainDateTime::create($updatedAt),
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function restaurantId(): RestaurantId
    {
        return $this->restaurantId;
    }

    public function orderId(): OrderId
    {
        return $this->orderId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function ticketNumber(): ?SaleTicketNumber
    {
        return $this->ticketNumber;
    }

    public function valueDate(): DomainDateTime
    {
        return $this->valueDate;
    }

    public function total(): SaleTotal
    {
        return $this->total;
    }

    public function createdAt(): DomainDateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DomainDateTime
    {
        return $this->updatedAt;
    }
}