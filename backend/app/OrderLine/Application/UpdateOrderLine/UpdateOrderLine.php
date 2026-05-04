<?php

namespace App\OrderLine\Application\UpdateOrderLine;

use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;
use App\OrderLine\Domain\ValueObject\OrderLinePrice;
use App\OrderLine\Domain\ValueObject\OrderLineQuantity;

final class UpdateOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
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

        $orderLine = $orderLine->update(
            $quantityVO,
            $priceVO,
            $orderLine->taxPercentage(),
        );

        $this->orderLineRepository->save($orderLine);

        return UpdateOrderLineResponse::create($orderLine);
    }
}