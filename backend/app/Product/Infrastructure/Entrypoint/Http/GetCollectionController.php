<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\ListProducts\ListProducts;
use App\Product\Infrastructure\Entrypoint\Http\Requests\ListProductsRequest;
use Illuminate\Http\JsonResponse;

final class GetCollectionController
{
    public function __construct(
        private ListProducts $listProducts,
    ) {}

    public function __invoke(ListProductsRequest $request): JsonResponse
    {
        try {
            $response = ($this->listProducts)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
