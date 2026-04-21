<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\GetProducts\GetProducts;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetProducts $getProducts,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getProducts)();

        return new JsonResponse($response->toArray(), 200);
    }
}
