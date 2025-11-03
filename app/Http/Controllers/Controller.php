<?php

namespace App\Http\Controllers;
use OpenApi\Attributes as OA;

#[
    OA\Info(
        version: "1.0.0",
        title: "API de Gerenciamento de Consultas",
        description: "Documentação da API para o projeto de agendamentos e serviços.",
        contact: new OA\Contact(
            email: "seu.email@dominio.com"
        )
    ),
    OA\Server(
        url: 'http://localhost:8000',
        description: 'Servidor de Desenvolvimento Local'
    ),
    OA\Schema(
        schema: "AuthResponse",
        title: "Auth Response",
        description: "Resposta de sucesso de Login ou Registro",
        properties: [
            new OA\Property(
                property: "user",
                ref: "#/components/schemas/UserResource"
            ),
            new OA\Property(
                property: "token",
                type: "string",
                example: "1|aBcDeFgHiJkLmNoPqRsTuVwXyZ123456"
            )
        ]
    ),
    OA\SecurityScheme(
        securityScheme: "bearerAuth",
        type: "http",
        scheme: "bearer",
        bearerFormat: "JWT",
        description: "Token Sanctum (obrigatório para endpoints protegidos)"
    )
]
abstract class Controller
{
    //
}
