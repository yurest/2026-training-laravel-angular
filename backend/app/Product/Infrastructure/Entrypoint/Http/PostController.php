<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\CreateProduct\CreateProduct;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateProduct $createProduct,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'family_id' => ['required', 'integer'],
            'tax_id' => ['required', 'integer'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_src' => ['nullable', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'price' => ['required', 'integer', 'min:0'],
        ]);

        $response = ($this->createProduct)(
            (string) $validated['restaurant_id'],
            (string) $validated['family_id'],
            (string) $validated['tax_id'],
            $validated['stock'],
            $validated['image_src'] ?? null,
            $validated['name'],
            $validated['price']
        );

        return new JsonResponse($response->toArray(), 201);
    }
}
