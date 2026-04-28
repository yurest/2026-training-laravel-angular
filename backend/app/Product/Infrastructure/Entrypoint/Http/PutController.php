<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\UpdateProduct\UpdateProduct;
use App\Product\Domain\Exception\ProductNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PutController
{
    public function __construct(
        private UpdateProduct $updateProduct,
    ) {}

    public function __invoke(Request $request, string $id): JsonResponse
    {
        $validated = $request->validate([
            'family_id' => ['sometimes', 'integer'],
            'tax_id' => ['sometimes', 'integer'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'image_src' => ['nullable', 'string', 'max:255'],
            'name' => ['sometimes', 'string', 'max:255'],
            'price' => ['sometimes', 'integer', 'min:0'],
            'active' => ['sometimes', 'boolean'],
        ]);

        try {
            $response = ($this->updateProduct)(
                $id,
                isset($validated['family_id']) ? (string) $validated['family_id'] : null,
                isset($validated['tax_id']) ? (string) $validated['tax_id'] : null,
                $validated['stock'] ?? null,
                $validated['image_src'] ?? null,
                $validated['name'] ?? null,
                $validated['price'] ?? null,
                $validated['active'] ?? null,
            );

            return new JsonResponse($response->toArray(), 200);
        } catch (ProductNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
