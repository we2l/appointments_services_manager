<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginAuthRequest;
use App\Http\Requests\RegisterAuthRequest;
use App\Services\Contracts\AuthServiceInterface;
use OpenApi\Attributes as OA;

#[OA\Tag(
    name: "Authentication",
    description: "Endpoints para registro e login de usuários"
)]
class AuthController extends Controller
{
    public function __construct(
        private AuthServiceInterface $authService
    )
    {}

    #[OA\Post(
        path: '/api/auth/register',
        operationId: 'registerUser',
        summary: 'Registra um novo usuário',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/RegisterAuthRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário registrado com sucesso',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/AuthResponse'
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Erro de validação (ex: e-mail já existe)'
            )
        ]
    )]
    public function register(RegisterAuthRequest $request)
    {
        $user = $this->authService->registerAuth($request->all());

        return response()->json($user, 201);
    }

    #[OA\Post(
        path: '/api/auth/login',
        operationId: 'loginUser',
        summary: 'Autentica um usuário e retorna um token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                ref: '#/components/schemas/LoginAuthRequest'
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Login bem-sucedido',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/AuthResponse'
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Credenciais inválidas'
            )
        ]
    )]
    public function login(LoginAuthRequest $request)
    {
        $user = $this->authService->loginAuth($request->all());
        return response()->json($user);
    }
}
