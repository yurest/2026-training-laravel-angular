<?php

namespace App\Restaurant\Domain\Entity;

use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\Email;
use App\Shared\Domain\ValueObject\Uuid;
use App\Restaurant\Domain\ValueObject\CompanyTaxId;
use App\Restaurant\Domain\ValueObject\RestaurantLegalName;
use App\Restaurant\Domain\ValueObject\RestaurantName;
use App\Restaurant\Domain\ValueObject\RestaurantPasswordHash;

class Restaurant
{
    private function __construct(
        private Uuid $id,
        private RestaurantName $name,
        private RestaurantLegalName $legalName,
        private CompanyTaxId $fiscalId,
        private Email $email,
        private RestaurantPasswordHash $passwordHash,
        private DomainDateTime $createdAt,
        private DomainDateTime $updatedAt,
    ) {}

    public static function dddCreate(
        RestaurantName $name,
        RestaurantLegalName $legalName,
        CompanyTaxId $fiscalId,
        Email $email,
        RestaurantPasswordHash $passwordHash,
    ): self {
        $now = DomainDateTime::now();

        return new self(
            Uuid::generate(),
            $name,
            $legalName,
            $fiscalId,
            $email,
            $passwordHash,
            $now,
            $now,
        );
    }

    public static function fromPersistence(
        string $id,
        string $name,
        string $legalName,
        string $taxId,
        string $email,
        string $passwordHash,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            Uuid::create($id),
            RestaurantName::create($name),
            RestaurantLegalName::create($legalName),
            CompanyTaxId::create($taxId),
            Email::create($email),
            RestaurantPasswordHash::create($passwordHash),
            DomainDateTime::create($createdAt),
            DomainDateTime::create($updatedAt),
        );
    }

    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): RestaurantName
    {
        return $this->name;
    }

    public function legalName(): RestaurantLegalName
    {
        return $this->legalName;
    }

    public function fiscalId(): CompanyTaxId
    {
        return $this->fiscalId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function passwordHash(): RestaurantPasswordHash
    {
        return $this->passwordHash;
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
        RestaurantName $name,
        RestaurantLegalName $legalName,
        CompanyTaxId $fiscalId,
        Email $email,
    ): self {
        return new self(
            $this->id,
            $name,
            $legalName,
            $fiscalId,
            $email,
            $this->passwordHash,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }

    public function changePassword(RestaurantPasswordHash $passwordHash): self
    {
        return new self(
            $this->id,
            $this->name,
            $this->legalName,
            $this->fiscalId,
            $this->email,
            $passwordHash,
            $this->createdAt,
            DomainDateTime::now(),
        );
    }
}