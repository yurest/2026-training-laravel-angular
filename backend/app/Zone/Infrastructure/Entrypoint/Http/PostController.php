<?php

namespace App\Zone\Infrastructure\Entrypoint\Http;

use App\Zone\Application\CreateZone\CreateZone;
use App\Zone\Infrastructure\Entrypoint\Http\Requests\CreateZoneRequest;
use Illuminate\Http\JsonResponse;

final class PostController
{
    public function __construct(
        private CreateZone $createZone,
    ) {}

    public function __invoke(CreateZoneRequest $request): JsonResponse
    {
        try {
            $response = ($this->createZone)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray(), 201);
    }
}
