<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        schema: "RegisterAuthRequest",
        title: "Register Auth Request",
        description: "Payload para registro de novo usuário",
        required: ["name", "email", "password"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "Weslley"),
            new OA\Property(property: "email", type: "string", format: "email", example: "weslley@exemplo.com"),
            new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
        ]
    )
]
class RegisterAuthRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ];
    }
}
