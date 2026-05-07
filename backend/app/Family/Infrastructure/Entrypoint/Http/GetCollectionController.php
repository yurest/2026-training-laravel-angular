<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\ListFamilies\ListFamilies;
use App\Family\Infrastructure\Entrypoint\Http\Requests\ListFamiliesRequest;
use Illuminate\Http\JsonResponse;

final class GetCollectionController
{
    public function __construct(
        private ListFamilies $listFamilies,
    ) {}

    public function __invoke(ListFamiliesRequest $request): JsonResponse
    {
        try {
            $response = ($this->listFamilies)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
