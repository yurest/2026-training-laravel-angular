<?php

namespace App\OrderLine\Application\GetOrderLines;

use App\OrderLine\Application\GetOrderLine\GetOrderLineResponse;

final readonly class GetOrderLinesResponse
{
    /**
     * @param array<int, GetOrderLineResponse> $orderLines
     */
    public function __construct(
        public array $orderLines,
    ) {}

    /**
     * @param array<int, GetOrderLineResponse> $orderLines
     */
    public static function create(array $orderLines): self
    {
        return new self($orderLines);
    }

    /**
     * @return array<string, array<int, array<string, string|int>>>
     */
    public function toArray(): array
    {
        return [
            'order_lines' => array_map(
                fn (GetOrderLineResponse $orderLine) => $orderLine->toArray(),
                $this->orderLines,
            ),
        ];
    }
}