<?php

namespace App\OrderLine\Infrastructure\Entrypoint\Http;

use App\OrderLine\Application\GetOrderLine\GetOrderLine;
use App\OrderLine\Domain\Exception\OrderLineNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetOrderLine $getOrderLine,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getOrderLine)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (OrderLineNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}