<?php

namespace App\Tax\Application\DeleteTax;

use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

final class DeleteTax
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(string $id): void
    {
        $tax = $this->taxRepository->findById($id);

        if ($tax === null) {
            throw TaxNotFoundException::withId($id);
        }

        $this->taxRepository->delete($tax);
    }
}
