<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\GetProduct\GetProduct;
use App\Product\Domain\Exception\ProductNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetProduct $getProduct,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getProduct)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (ProductNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
