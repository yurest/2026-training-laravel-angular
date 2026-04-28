<?php

namespace App\Tax\Application\CreateTax;

use App\Shared\Domain\ValueObject\RestaurantId;
use App\Tax\Domain\Entity\Tax;
use App\Tax\Domain\Interfaces\TaxRepositoryInterface;
use App\Tax\Domain\ValueObject\TaxName;
use App\Tax\Domain\ValueObject\TaxPercentage;

final class CreateTax
{
    public function __construct(
        private TaxRepositoryInterface $taxRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $name,
        int $percentage
    ): CreateTaxResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $nameVO = TaxName::create($name);
        $percentageVO = TaxPercentage::create($percentage);

        $tax = Tax::dddCreate(
            $restaurantIdVO,
            $nameVO,
            $percentageVO
        );
        $this->taxRepository->save($tax);

        return CreateTaxResponse::create($tax);
    }
}
