<?php

namespace App\Family\Infrastructure\Entrypoint\Http;

use App\Family\Application\SetFamilyActive\SetFamilyActive;
use App\Family\Domain\Exception\FamilyNotFoundException;
use App\Family\Infrastructure\Entrypoint\Http\Requests\SetFamilyActiveRequest;
use Illuminate\Http\JsonResponse;

final class DeactivateController
{
    public function __construct(
        private SetFamilyActive $setFamilyActive,
    ) {}

    public function __invoke(SetFamilyActiveRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->setFamilyActive)($request->toCommand($id, false));
        } catch (FamilyNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
