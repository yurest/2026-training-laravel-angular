<?php

namespace App\OrderLine\Application\GetOrderLine;

use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;

final class GetOrderLine
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
    ) {}

    public function __invoke(string $id): GetOrderLineResponse
    {
        $orderLine = $this->orderLineRepository->findById($id);

        if ($orderLine === null) {
            throw OrderLineNotFoundException::withId($id);
        }

        return GetOrderLineResponse::create($orderLine);
    }
}