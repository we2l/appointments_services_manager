<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        schema: "UpdateServicesRequest",
        title: "Update Service Request",
        description: "Payload para atualizar um serviço",
        properties: [
            new OA\Property(property: "name", type: "string", example: "Equipmento"),
            new OA\Property(property: "price", type: "number", format: "float", example: 150.00)
        ]
    )
]
class UpdateServicesRequest extends FormRequest
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
            'name'  => 'string|min:3|max:255',
            'price' => 'numeric|min:0',
        ];
    }
}
