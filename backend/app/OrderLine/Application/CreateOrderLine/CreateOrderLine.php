<?php

namespace App\OrderLine\Application\CreateOrderLine;

use App\OrderLine\Domain\Entity\OrderLine;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\OrderLine\Domain\ValueObject\OrderLinePrice;
use App\OrderLine\Domain\ValueObject\OrderLineQuantity;
use App\OrderLine\Domain\ValueObject\OrderLineTaxPercentage;
use App\Shared\Domain\ValueObject\OrderId;
use App\Shared\Domain\ValueObject\ProductId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\UserId;

final class CreateOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $orderId,
        string $productId,
        string $userId,
        int $quantity,
        int $price,
        int $taxPercentage,
    ): CreateOrderLineResponse {
        $restaurantIdVO = RestaurantId::create($restaurantId);
        $orderIdVO = OrderId::create($orderId);
        $productIdVO = ProductId::create($productId);
        $userIdVO = UserId::create($userId);
        $quantityVO = OrderLineQuantity::create($quantity);
        $priceVO = OrderLinePrice::create($price);
        $taxPercentageVO = OrderLineTaxPercentage::create($taxPercentage);

        $orderLine = OrderLine::dddCreate(
            $restaurantIdVO,
            $orderIdVO,
            $productIdVO,
            $userIdVO,
            $quantityVO,
            $priceVO,
            $taxPercentageVO,
        );

        $this->orderLineRepository->save($orderLine);

        return CreateOrderLineResponse::create($orderLine);
    }
}