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
            'restaurant_id' => ['required', 'string'],
            'zone_id' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $response = ($this->createTable)(
            $validated['restaurant_id'],
            $validated['zone_id'],
            $validated['name'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}