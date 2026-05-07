<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\DeleteProduct\DeleteProduct;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Infrastructure\Entrypoint\Http\Requests\DeleteProductRequest;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteProduct $deleteProduct,
    ) {}

    public function __invoke(DeleteProductRequest $request, string $id): JsonResponse
    {
        try {
            ($this->deleteProduct)($request->toCommand($id));
        } catch (ProductNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse(null, 204);
    }
}
