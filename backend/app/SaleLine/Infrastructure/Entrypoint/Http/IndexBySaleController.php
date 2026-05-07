<?php

namespace App\SaleLine\Infrastructure\Entrypoint\Http;

use App\SaleLine\Application\GetSaleLinesBySale\GetSaleLinesBySale;
use Illuminate\Http\JsonResponse;

final class IndexBySaleController
{
    public function __construct(
        private GetSaleLinesBySale $getSaleLinesBySale,
    ) {}

    public function __invoke(string $saleId): JsonResponse
    {
        $response = ($this->getSaleLinesBySale)($saleId);

        return new JsonResponse($response->toArray(), 200);
    }
}
