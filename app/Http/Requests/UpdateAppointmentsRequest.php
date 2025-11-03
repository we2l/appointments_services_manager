<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;
#[
    OA\Schema(
        schema: "UpdateAppointmentsRequest",
        title: "Update Appointments Request",
        description: "Payload para atualizar um agendamento",
        properties: [
            new OA\Property(property: "title", type: "string", example: "Consulta de Retorno"),
            new OA\Property(property: "scheduled_at", type: "string", format: "date-time", example: "2025-11-15T10:00:00Z"),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "status", type: "string", example: "confirmed"),
            new OA\Property(
                property: "services",
                type: "array",
                items: new OA\Items(type: "integer"),
                example: [3]
            )
        ]
    )
]
class UpdateAppointmentsRequest extends FormRequest
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
            'title' => 'string|max:255|min:3',
            'user_id' => 'exists:users,id',
            'scheduled_at' => 'date',
            'status' => 'in:pending,confirmed,cancelled',
            'services' => 'sometimes|present|array',
            'services.*' => 'integer|exists:services,id'
        ];
    }
}
