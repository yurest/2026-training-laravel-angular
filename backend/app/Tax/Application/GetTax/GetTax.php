<?php

namespace App\Tax\Application\GetTax;

use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;

final class GetTax
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(string $id): GetTaxResponse
    {
        $tax = $this->taxRepository->findById($id);

        if ($tax === null) {
            throw TaxNotFoundException::withId($id);
        }

        return GetTaxResponse::create($tax);
    }
}
