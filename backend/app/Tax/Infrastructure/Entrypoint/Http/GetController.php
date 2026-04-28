<?php

namespace App\Tax\Infrastructure\Entrypoint\Http;

use App\Tax\Application\GetTax\GetTax;
use App\Tax\Domain\Exception\TaxNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetTax $getTax,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getTax)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (TaxNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}
