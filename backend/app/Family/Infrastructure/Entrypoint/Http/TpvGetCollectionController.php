<?php

declare(strict_types=1);

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\ListActiveFamilies\ListActiveFamilies;
use App\Family\Infrastructure\Entrypoint\Http\Requests\ListActiveFamiliesRequest;
use Illuminate\Http\JsonResponse;

final class TpvGetCollectionController
{
    public function __construct(
        private ListActiveFamilies $listActiveFamilies,
    ) {}

    public function __invoke(ListActiveFamiliesRequest $request): JsonResponse
    {
        try {
            $response = ($this->listActiveFamilies)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
