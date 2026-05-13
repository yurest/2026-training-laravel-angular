<?php

namespace App\Restaurant\Application\ValidateRestaurantCompanyMode;

use App\Restaurant\Domain\Exception\TaxIdAlreadyExistsException;
use App\Restaurant\Domain\Exception\TaxIdDoesNotExistException;
use App\Restaurant\Domain\Interfaces\RestaurantRepositoryInterface;

final class ValidateRestaurantCompanyMode
{
    public function __construct(
        private RestaurantRepositoryInterface $restaurantRepository,
    ) {}

    public function __invoke(string $taxId, string $companyMode): void
    {
        $companyExists = count($this->restaurantRepository->findByTaxId($taxId)) > 0;

        if ($companyMode === 'new' && $companyExists) {
            throw TaxIdAlreadyExistsException::create($taxId);
        }

        if ($companyMode === 'existing' && ! $companyExists) {
            throw TaxIdDoesNotExistException::create($taxId);
        }
    }
}
