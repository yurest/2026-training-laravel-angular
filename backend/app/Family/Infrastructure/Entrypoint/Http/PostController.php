<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\CreateFamily\CreateFamily;
use App\Family\Infrastructure\Entrypoint\Http\Requests\CreateFamilyRequest;
use Illuminate\Http\JsonResponse;

final class PostController
{
    public function __construct(
        private CreateFamily $createFamily,
    ) {}

    public function __invoke(CreateFamilyRequest $request): JsonResponse
    {
        try {
            $response = ($this->createFamily)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray(), 201);
    }
}
