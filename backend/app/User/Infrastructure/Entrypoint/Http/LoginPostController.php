<?php

namespace App\User\Infrastructure\Entrypoint\Http;

use App\User\Application\LoginUser\LoginUser;
use App\User\Domain\Exception\UserInvalidCredentialsException;
use App\User\Infrastructure\Persistence\Models\EloquentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class LoginPostController
{
    public function __construct(
        private LoginUser $loginUser,
    ) {}

    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        try {
            $response = ($this->loginUser)(
                $validated['email'],
                $validated['password'],
            );

            $authUser = EloquentUser::query()
                ->where('email', $validated['email'])
                ->firstOrFail();

            $token = $authUser->createToken('frontend')->plainTextToken;

            return new JsonResponse([
                'token'=> $token,
                'user' => $response->toArray(),
            ], 200);
            
        } catch (UserInvalidCredentialsException $exception) {
            return new JsonResponse(['message' => $exception->getMessage()], 401);
        }
    }
}
