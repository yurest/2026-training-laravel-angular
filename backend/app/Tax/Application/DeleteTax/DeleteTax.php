<?php

namespace App\Tax\Application\DeleteTax;

use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

class DeleteTax
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(DeleteTaxCommand $command): void
    {
        $tax = $this->taxRepository->findById($command->id)
            ?? throw TaxNotFoundException::withId($command->id);

        $this->taxRepository->deleteById($command->id);
    }
}
