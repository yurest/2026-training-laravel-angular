<?php

declare(strict_types=1);

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\ListActiveProducts\ListActiveProducts;
use App\Product\Infrastructure\Entrypoint\Http\Requests\ListActiveProductsRequest;
use Illuminate\Http\JsonResponse;

final class TpvGetCollectionController
{
    public function __construct(
        private ListActiveProducts $listActiveProducts,
    ) {}

    public function __invoke(ListActiveProductsRequest $request): JsonResponse
    {
        try {
            $response = ($this->listActiveProducts)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
