<?php

namespace App\Sale\Application\CreateSale;

use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Sale\Domain\ValueObject\SaleTotal;
use App\SaleLine\Domain\Entity\SaleLine;
use App\SaleLine\Domain\Interfaces\SaleLineRepositoryInterface;
use App\SaleLine\Domain\ValueObject\SaleLinePrice;
use App\SaleLine\Domain\ValueObject\SaleLineQuantity;
use App\SaleLine\Domain\ValueObject\SaleLineTaxPercentage;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\OrderLineId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\SaleId;
use App\Shared\Domain\ValueObject\UserId;

final class CreateSale
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private OrderLineRepositoryInterface $orderLineRepository,
        private SaleLineRepositoryInterface $saleLineRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $orderId,
        string $userId,
    ): CreateSaleResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $orderIdVO = OrderId::create($orderId);
        $userIdVO = UserId::create($userId);

        $orderLines = $this->orderLineRepository->findByOrderId($orderId);

        $total = 0;

        foreach ($orderLines as $orderLine) {
            $total += $orderLine->price()->value() * $orderLine->quantity()->value();
        }

        $sale = Sale::dddCreate(
            $restaurantIdVO,
            $orderIdVO,
            $userIdVO,
            DomainDateTime::now(),
            SaleTotal::create($total),
        );

        $this->saleRepository->save($sale);

        $saleIdVO = SaleId::create($sale->id()->value());

        foreach ($orderLines as $orderLine) {
            $saleLine = SaleLine::dddCreate(
                $restaurantIdVO,
                $saleIdVO,
                OrderLineId::create($orderLine->id()->value()),
                $userIdVO,
                SaleLineQuantity::create($orderLine->quantity()->value()),
                SaleLinePrice::create($orderLine->price()->value()),
                SaleLineTaxPercentage::create($orderLine->taxPercentage()->value()),
            );

            $this->saleLineRepository->save($saleLine);
        }

        return CreateSaleResponse::create($sale);
    }
}