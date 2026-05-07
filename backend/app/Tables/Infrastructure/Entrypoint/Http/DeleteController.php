<?php

namespace App\Tables\Infrastructure\Entrypoint\Http;

use App\Tables\Application\DeleteTable\DeleteTable;
use App\Tables\Domain\Exception\TableNotFoundException;
use App\Tables\Infrastructure\Entrypoint\Http\Requests\DeleteTableRequest;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteTable $deleteTable,
    ) {}

    public function __invoke(DeleteTableRequest $request, string $id): JsonResponse
    {
        try {
            ($this->deleteTable)($request->toCommand($id));
        } catch (TableNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse(null, 204);
    }
}
