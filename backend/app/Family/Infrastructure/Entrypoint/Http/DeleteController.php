<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\DeleteFamily\DeleteFamily;
use App\Family\Domain\Exception\FamilyNotFoundException;
use App\Family\Infrastructure\Entrypoint\Http\Requests\DeleteFamilyRequest;
use Illuminate\Http\JsonResponse;

final class DeleteController
{
    public function __construct(
        private DeleteFamily $deleteFamily,
    ) {}

    public function __invoke(DeleteFamilyRequest $request, string $id): JsonResponse
    {
        try {
            ($this->deleteFamily)($request->toCommand($id));
        } catch (FamilyNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse(null, 204);
    }
}
