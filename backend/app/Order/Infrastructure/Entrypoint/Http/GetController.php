<?php

namespace App\Order\Infrastructure\Entrypoint\Http;

use App\Order\Application\GetOrder\GetOrder;
use App\Order\Domain\Exception\OrderNotFoundException;
use Illuminate\Http\JsonResponse;

final class GetController
{
    public function __construct(
        private GetOrder $getOrder,
    ) {}

    public function __invoke(string $id): JsonResponse
    {
        try {
            $response = ($this->getOrder)($id);

            return new JsonResponse($response->toArray(), 200);
        } catch (OrderNotFoundException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 404);
        }
    }
}