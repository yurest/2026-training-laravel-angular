<?php

namespace App\Sale\Application\CreateSale;

use App\Sale\Domain\Entity\Sale;
use App\Sale\Domain\Interfaces\SaleRepositoryInterface;
use App\Sale\Domain\ValueObject\SaleTotal;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\UserId;

final class CreateSale
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $orderId,
        string $userId,
        int $total,
    ): CreateSaleResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $orderIdVO = OrderId::create($orderId);
        $userIdVO = UserId::create($userId);
        $totalVO = SaleTotal::create($total);

        $sale = Sale::dddCreate(
            $restaurantIdVO,
            $orderIdVO,
            $userIdVO,
            DomainDateTime::now(),
            $totalVO,
        );

        $this->saleRepository->save($sale);

        return CreateSaleResponse::create($sale);
    }
}