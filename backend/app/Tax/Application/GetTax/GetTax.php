<?php

namespace App\Tax\Application\GetTax;

use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

class GetTax
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(GetTaxCommand $command): GetTaxResponse
    {
        $tax = $this->taxRepository->findById($command->id);

        if ($tax === null) {
            throw TaxNotFoundException::withId($command->id);
        }

        return GetTaxResponse::create(
            id: $tax->id()->value(),
            name: $tax->name()->value(),
            percentage: $tax->percentage()->value(),
            createdAt: $tax->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $tax->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
