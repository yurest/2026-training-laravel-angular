<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\CreateTax\CreateTax;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateTax $createTax,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'percentage' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $response = ($this->createTax)(
            (string) $validated['restaurant_id'],
            $validated['name'],
            $validated['percentage'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}
