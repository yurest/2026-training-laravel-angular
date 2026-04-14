<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\DeleteProduct\DeleteProduct;
use App\Product\Domain\Exception\ProductNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteProduct $deleteProduct,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteProduct)($id);

            return new JsonResponse(null, 204);
        } catch (ProductNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}