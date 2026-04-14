<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\CreateTable\CreateTable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateTable $createTable,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'zone_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $response = ($this->createTable)(
            (string) $validated['restaurant_id'],
            (string) $validated['zone_id'],
            $validated['name'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}