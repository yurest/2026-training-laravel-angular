<?php

namespace App\Tables\Infrastructure\Entrypoint\Http;

use App\Tables\Application\GetTable\GetTable;
use App\Tables\Domain\Exception\TableNotFoundException;
use App\Tables\Infrastructure\Entrypoint\Http\Requests\GetTableRequest;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetTable $getTable,
    ) {}

    public function __invoke(GetTableRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->getTable)($request->toCommand($id));
        } catch (TableNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
