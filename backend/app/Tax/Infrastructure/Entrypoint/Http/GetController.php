<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\GetTax\GetTax;
use App\Tax\Domain\Exception\TaxNotFoundException;
use App\Tax\Infrastructure\Entrypoint\Http\Requests\GetTaxRequest;
use Illuminate\Http\JsonResponse;

class GetController
{
    public function __construct(
        private GetTax $getTax,
    ) {}

    public function __invoke(GetTaxRequest $request, string $id): JsonResponse
    {
        try {
            $response = ($this->getTax)($request->toCommand($id));
        } catch (TaxNotFoundException $e) {
            return new JsonResponse(['message' => $e->getMessage()], 404);
        } catch (\Throwable $e) {
            report($e);

            return new JsonResponse(['message' => 'Internal error.'], 500);
        }

        return new JsonResponse($response->toArray());
    }
}
