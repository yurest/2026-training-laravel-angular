<?php

namespace App\Tables\Infrastructure\Entrypoint\Http;

use App\Tables\Application\ListTables\ListTables;
use App\Tables\Infrastructure\Entrypoint\Http\Requests\ListTablesRequest;
use Illuminate\Http\JsonResponse;

final class GetCollectionController
{
    public function __construct(
        private ListTables $listTables,
    ) {}

    public function __invoke(ListTablesRequest $request): JsonResponse
    {
        try {
            $response = ($this->listTables)($request->toCommand());
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
