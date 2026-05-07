<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\CreateProduct\CreateProduct;
use App\Product\Infrastructure\Entrypoint\Http\Requests\CreateProductRequest;
use Illuminate\Http\JsonResponse;

final class PostController
{
    public function __construct(
        private CreateProduct $createProduct,
    ) {}

    public function __invoke(CreateProductRequest $request): JsonResponse
    {
        try {
            $response = ($this->createProduct)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray(), 201);
    }
}
