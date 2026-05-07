<?php

namespace App\Product\Infrastructure\Entrypoint\Http;

use App\Product\Application\SetProductActive\SetProductActive;
use App\Product\Domain\Exception\ProductNotFoundException;
use App\Product\Infrastructure\Entrypoint\Http\Requests\SetProductActiveRequest;
use Illuminate\Http\JsonResponse;

final class ActivateController
{
    public function __construct(
        private SetProductActive $setProductActive,
    ) {}

    public function __invoke(SetProductActiveRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->setProductActive)($request->toCommand($id, true));
        } catch (ProductNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
