<?php

namespace App\SaleLine\Domain\Interfaces;

use App\SaleLine\Domain\Entity\SaleLine;

interface SaleLineRepositoryInterface
{
    public function save(SaleLine $saleLine): void;
}