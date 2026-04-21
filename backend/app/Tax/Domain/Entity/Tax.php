<?php

namespace App\Tax\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\Uuid;
use App\Tax\Domain\ValueObject\TaxName;
use App\Tax\Domain\ValueObject\TaxPercentage;

class Tax
{
    private function __construct(
        private Uuid $id,
        private RestaurantId $restaurantId,
        private TaxName $name,
        private TaxPercentage $percentage,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantId $restaurantId,
        TaxName $name,
        TaxPercentage $percentage,
    ): self {

        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $restaurantId,
            $name,
            $percentage,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $restaurantId,
        string $name,
        int $percentage,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantId::create($restaurantId),
            TaxName::create($name),
            TaxPercentage::create($percentage),
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

    public function name(): TaxName
    {
        return $this->name;
    }

    public function percentage(): TaxPercentage
    {
        return $this->percentage;
    }

    public function createdAt(): DomainDateTime
    {
        return $this->createdAt;
    }

    public function updatedAt(): DomainDateTime
    {
        return $this->updatedAt;
    }

    public function update(
        TaxName $name,
        TaxPercentage $percentage,
    ): self {
        return new self(
            $this->id,
            $this->restaurantId,
            $name,
            $percentage,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }
}
