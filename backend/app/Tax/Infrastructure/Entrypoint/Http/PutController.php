<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\UpdateTax\UpdateTax;
use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Infrastructure\Entrypoint\Http\Requests\UpdateTaxRequest;
use Illuminate\Http\JsonResponse;

class PutController
{
    public function __construct(
        private UpdateTax $updateTax,
    ) {}

    public function __invoke(UpdateTaxRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->updateTax)($request->toCommand($id));
        } catch (TaxNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
