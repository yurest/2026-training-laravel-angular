<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\DeleteFamily\DeleteFamily;
use App\Family\Domain\Exception\FamilyNotFoundException;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteFamily $deleteFamily,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            ($this->deleteFamily)($id);

            return new JsonResponse(null, 204);
        } catch (FamilyNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
