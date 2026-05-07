<?php

namespace App\OrderLine\Application\UpdateOrderLine;

use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\OrderLine\Domain\ValueObject\OrderLinePrice;
use App\OrderLine\Domain\ValueObject\OrderLineQuantity;
use App\OrderLineLog\Application\CreateOrderLineLog\CreateOrderLineLog;

final class UpdateOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
        private CreateOrderLineLog $createOrderLineLog,
    ) {}

    public function __invoke(
        string $id,
        ?int $quantity = null,
        ?int $price = null,
    ): UpdateOrderLineResponse {
        $orderLine = $this->orderLineRepository->findById($id);

        if ($orderLine === null) {
            throw OrderLineNotFoundException::withId($id);
        }

        $quantityVO = $quantity !== null
            ? OrderLineQuantity::create($quantity)
            : $orderLine->quantity();

        $priceVO = $price !== null
            ? OrderLinePrice::create($price)
            : $orderLine->price();

         // LOG (ANTES del update)
        ($this->createOrderLineLog)(
            $orderLine->restaurantId()->value(),
            $orderLine->orderId()->value(),
            $orderLine->id()->value(),
            $orderLine->userId()->value(),
            'updated',
            $orderLine->quantity()->value(),
            $quantityVO->value(),
            $orderLine->price()->value(),
            $priceVO->value(),
            null,
        );

        $orderLine = $orderLine->update(
            $quantityVO,
            $priceVO,
            $orderLine->taxPercentage(),
        );

        $this->orderLineRepository->save($orderLine);

        return UpdateOrderLineResponse::create($orderLine);
    }
}