<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[
    OA\Schema(
        schema: "StoreServicesRequest",
        title: "Store Service Request",
        description: "Payload para criar um novo serviço",
        required: ["name", "price"],
        properties: [
            new OA\Property(property: "name", type: "string", example: "Equipamento"),
            new OA\Property(property: "price", type: "number", format: "float", example: 120.50)
        ]
    )
]
class StoreServicesRequest extends FormRequest
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
            'name'  => 'required|min:3|max:255|string',
            'price' => 'required|numeric|min:0'
        ];
    }
}
