<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\GetProduct\GetProduct;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Infrastructure\Entrypoint\Http\Requests\GetProductRequest;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetProduct $getProduct,
    ) {}

    public function __invoke(GetProductRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->getProduct)($request->toCommand($id));
        } catch (ProductNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
