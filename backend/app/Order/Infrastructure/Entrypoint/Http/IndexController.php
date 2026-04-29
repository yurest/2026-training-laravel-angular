<?php

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\GetOrders\GetOrders;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetOrders $getOrders,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getOrders)();

        return new JsonResponse($response->toArray(), 200);
    }
}