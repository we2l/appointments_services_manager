<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;
#[
    OA\Schema(
        schema: "StoreAppointmentsRequest",
        title: "Store Appointments Request",
        description: "Payload para criar uma nova consulta",
        required: ["title", "scheduled_at", "user_id", "services"],
        properties: [
            new OA\Property(property: "title", type: "string", example: "Consulta de Rotina"),
            new OA\Property(property: "scheduled_at", type: "string", format: "date-time", example: "2025-10-30T14:30:00Z"),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "status", type: "string", example: "pending"),
            new OA\Property(
                property: "services",
                type: "array",
                items: new OA\Items(type: "integer"),
                example: [1, 2]
            )
        ]
    )
]
class StoreAppointmentsRequest extends FormRequest
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
            'title'         => 'required|string|max:255|min:3|unique:appointments,title',
            'user_id'       => 'required|exists:users,id',
            'scheduled_at'  => 'required|date',
            'status'        => 'required|in:pending,confirmed,cancelled',
            'services'      => 'present|array',
            'services.*'    => 'integer|exists:services,id',
        ];
    }
}
