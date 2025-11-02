<?php

namespace Tests\Unit\Services;

use App\Enums\MessageExceptionEnum;
use App\Exceptions\AuthException;
use App\Models\User;
use App\Repositories\Contracts\AuthRepositoryInterface;
use App\Services\AuthService;
use Laravel\Sanctum\NewAccessToken;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Illuminate\Contracts\Hashing\Hasher;

class AuthServiceTest extends TestCase
{
    private MockObject|AuthRepositoryInterface $authRepositoryMock;
    private MockObject|NewAccessToken $newAccessTokenMock;
    private MockObject|User $userMock;
    private MockObject|Hasher $hasherMock;

    public function setUp(): void
    {
        parent::setUp();
        $this->authRepositoryMock = $this->createMock(AuthRepositoryInterface::class);
        $this->newAccessTokenMock = $this->createMock(NewAccessToken::class);
        $this->userMock = $this->createMock(User::class);
        $this->hasherMock = $this->createMock(Hasher::class);
    }

    public function test_should_be_create_user(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
            'password' => 'teste123',
        ];

        $authService = $this->authInstance();
        $mockToken = $this->newAccessTokenMock;
        $mockToken->plainTextToken = "yX8nFZQv2pWJk5LahYc4t0Eo9NqMbS7d3GfLxRzPiU2vTrB1Ca";
        $mockUser = $this->createMock(User::class);

        $this->authRepositoryMock->expects($this->once())
            ->method('createUser')
            ->with($data)
            ->willReturn($mockUser);

        $mockUser->expects($this->once())
            ->method('createToken')
            ->with('auth_token')
            ->willReturn($mockToken);

        $result = $authService->registerAuth($data);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('user', $result);
    }

    public function test_login_should_throw_exception_if_user_not_found(): void
    {
        $data = [
            'email' => 'wrong@email.com',
            'password' => 'teste123'
        ];

        $this->authRepositoryMock->expects($this->once())
            ->method('getUserByEmail')
            ->with('wrong@email.com')
            ->willReturn(null);
        $this->hasherMock->expects($this->never())->method('check');

        $this->expectException(AuthException::class);
        $this->expectExceptionMessage(MessageExceptionEnum::INVALID_CREDENTIAL->value);

        $this->authInstance()->loginAuth($data);
    }

    public function test_login_should_be_successful(): void
    {
        $data = [
            'email' => 'john@doe.com',
            'password' => 'teste123'
        ];
        $hashedPassword = 'hashed_password_string';
        $expectedToken = 'yX8nFZQv2pWJk5LahYc4t0Eo9NqMbS7d3GfLxRzPiU2vTrB1Ca';

        $mockToken = $this->createMock(NewAccessToken::class);
        $mockToken->plainTextToken = $expectedToken;

        $mockUser = $this->createMock(User::class);

        $mockUser->method('__get')
            ->with('password')
            ->willReturn($hashedPassword);

        $mockUser->expects($this->once())
            ->method('createToken')
            ->with('auth_token')
            ->willReturn($mockToken);

        $this->authRepositoryMock->expects($this->once())
            ->method('getUserByEmail')
            ->with('john@doe.com')
            ->willReturn($mockUser);

        $this->hasherMock->expects($this->once())
            ->method('check')
            ->with('teste123', $hashedPassword)
            ->willReturn(true);

        $result = $this->authInstance()->loginAuth($data);

        $this->assertEquals([
            'user' => $mockUser,
            'token' => $expectedToken
        ], $result);
    }

    private function authInstance()
    {
        return new AuthService(
            $this->authRepositoryMock,
            $this->hasherMock,
        );
    }
}
