<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        schema: "LoginAuthRequest",
        title: "Login Auth Request",
        description: "Payload para login de usuário",
        required: ["email", "password"],
        properties: [
            new OA\Property(property: "email", type: "string", format: "email", example: "usuario@exemplo.com"),
            new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
        ]
    )
]
class LoginAuthRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string'
        ];
    }
}
