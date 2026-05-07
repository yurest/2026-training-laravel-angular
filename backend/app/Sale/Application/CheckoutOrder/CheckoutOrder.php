<?php

namespace App\Sale\Application\CheckoutOrder;

use App\Order\Domain\Exception\OrderNotFoundException;
use App\Order\Domain\Interfaces\OrderRepositoryInterface;
use App\Order\Domain\ValueObject\OrderStatus;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\Sale\Application\CreateSale\CreateSale;
use App\SaleLine\Domain\Entity\SaleLine;
use App\SaleLine\Domain\Interfaces\SaleLineRepositoryInterface;
use App\SaleLine\Domain\ValueObject\SaleLinePrice;
use App\SaleLine\Domain\ValueObject\SaleLineQuantity;
use App\SaleLine\Domain\ValueObject\SaleLineTaxPercentage;
use App\Shared\Domain\ValueObject\DomainDateTime;
use App\Shared\Domain\ValueObject\OrderLineId;
use App\Shared\Domain\ValueObject\RestaurantId;
use App\Shared\Domain\ValueObject\SaleId;
use App\Shared\Domain\ValueObject\UserId;

final class CheckoutOrder
{
    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private OrderLineRepositoryInterface $orderLineRepository,
        private SaleLineRepositoryInterface $saleLineRepository,
        private CreateSale $createSale,
    ) {}

    public function __invoke(
        string $restaurantId,
        string $orderId,
        string $userId,
    ): CheckoutOrderResponse {
        $order = $this->orderRepository->findById($orderId);

        if ($order === null) {
            throw OrderNotFoundException::withId($orderId);
        }

        $orderLines = $this->orderLineRepository->findByOrderId($orderId);

        $total = 0;

        foreach ($orderLines as $orderLine) {
            $total += $orderLine->price()->value() * $orderLine->quantity()->value();
        }

        $saleResponse = ($this->createSale)(
            $restaurantId,
            $orderId,
            $userId,
            $total,
        );

        $restaurantIdVO = RestaurantId::create($restaurantId);
        $saleIdVO = SaleId::create($saleResponse->id);
        $userIdVO = UserId::create($userId);

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

        $closedOrder = $order->update(
            OrderStatus::invoiced(),
            $order->tableId(),
            $userIdVO,
            $order->diners(),
            DomainDateTime::now(),
        );

        $this->orderRepository->save($closedOrder);

        return CheckoutOrderResponse::create(
            $saleResponse->id,
            $restaurantId,
            $orderId,
            $userId,
            $total,
        );
    }
} 