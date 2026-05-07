<?php

namespace App\OrderLine\Application\GetOrderLines;

use App\OrderLine\Application\GetOrderLine\GetOrderLineResponse;
use App\OrderLine\Domain\Interfaces\OrderLineRepositoryInterface;

final class GetOrderLines
{
    public function __construct(
        private OrderLineRepositoryInterface $orderLineRepository,
    ) {}

    public function __invoke(): GetOrderLinesResponse
    {
        $orderLines = $this->orderLineRepository->findAll();

        $response = array_map(
            fn ($orderLine) => GetOrderLineResponse::create($orderLine),
            $orderLines,
        );

        return GetOrderLinesResponse::create($response);
    }
}