<?php

namespace App\Table\Infrastructure\Entrypoint\Http;

use App\Table\Application\DeleteTable\DeleteTable;
use App\Table\Domain\Exception\TableNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteTable $deleteTable,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteTable)($id);

            return new JsonResponse(null, 204);
        } catch (TableNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
