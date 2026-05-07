<?php

namespace App\Tax\Application\UpdateTax;

use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;
use App\Tax\Domain\ValueObject\TaxName;
use App\Tax\Domain\ValueObject\TaxPercentage;

class UpdateTax
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(UpdateTaxCommand $command): UpdateTaxResponse
    {
        $tax = $this->taxRepository->findById($command->id);

        if ($tax === null) {
            throw TaxNotFoundException::withId($command->id);
        }

        $tax->update(
            $command->name !== null ? TaxName::create($command->name) : null,
            $command->percentage !== null ? TaxPercentage::create($command->percentage) : null,
        );
        $this->taxRepository->save($tax);

        return UpdateTaxResponse::create(
            id: $tax->id()->value(),
            name: $tax->name()->value(),
            percentage: $tax->percentage()->value(),
            createdAt: $tax->createdAt()->format(\DateTimeInterface::ATOM),
            updatedAt: $tax->updatedAt()->format(\DateTimeInterface::ATOM),
        );
    }
}
