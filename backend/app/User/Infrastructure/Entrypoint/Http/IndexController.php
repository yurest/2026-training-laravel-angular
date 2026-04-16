<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\GetUsers\GetUsers;
use Illuminate\Http\JsonResponse;

final class IndexController
{
    public function __construct(
        private GetUsers $getUsers,
    ) {}

    public function __invoke(): JsonResponse
    {
        $response = ($this->getUsers)();

        return new JsonResponse($response->toArray(), 200);
    }
}