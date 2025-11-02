<?php

namespace App\Services;

use App\Enums\MessageExceptionEnum;
use App\Exceptions\AuthException;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Contracts\Hashing\Hasher;

readonly class AuthService implements AuthServiceInterface
{
    public function __construct(
        private AuthRepositoryInterface $authRepository,
        private Hasher $hasher
    )
    {}
    public function registerAuth(array $data): array
    {
        $user = $this->authRepository->createUser($data);
        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    public function loginAuth(array $data): array
    {
        $user = $this->authRepository->getUserByEmail($data['email']);
        if (!$user || !$this->hasher->check($data['password'], $user->password)) {
            throw new AuthException(MessageExceptionEnum::INVALID_CREDENTIAL->value);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }
}
