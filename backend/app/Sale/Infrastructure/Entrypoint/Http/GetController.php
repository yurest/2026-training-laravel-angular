<?php

namespace App\Sale\Infrastructure\Entrypoint\Http;

use App\Sale\Application\GetSale\GetSale;
use App\Sale\Domain\Exception\SaleNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetSale $getSale,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getSale)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (SaleNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}