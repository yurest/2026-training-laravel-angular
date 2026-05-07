<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\UpdateProduct\UpdateProduct;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Infrastructure\Entrypoint\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;

final class PutController
{
    public function __construct(
        private UpdateProduct $updateProduct,
    ) {}

    public function __invoke(UpdateProductRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->updateProduct)($request->toCommand($id));
        } catch (ProductNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
