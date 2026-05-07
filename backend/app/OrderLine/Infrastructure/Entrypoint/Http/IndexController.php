<?php

namespace App\OrderLine\Infrastructure\Entrypoint\Http;

use App\OrderLine\Application\GetOrderLines\GetOrderLines;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetOrderLines $getOrderLines,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getOrderLines)();

        return new JsonResponse($response->toArray(), 200);
    }
}