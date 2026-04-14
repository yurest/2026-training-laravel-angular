<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\CreateZone\CreateZone;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class PostController
{
    public function __construct(
        private CreateZone $createZone,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'restaurant_id' => ['required', 'integer'],
            'name' => ['required', 'string', 'max:255'],
        ]);

        $response = ($this->createZone)(
            (string) $validated['restaurant_id'],
            $validated['name'],
        );

        return new JsonResponse($response->toArray(), 201);
    }
}