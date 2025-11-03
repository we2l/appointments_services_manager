<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
#[
    OA\Schema(
        schema: "AppointmentsResource",
        title: "Appointments Resource",
        description: "Representação de um agendamento",
        properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "title", type: "string", example: "Consulta de Rotina"),
            new OA\Property(property: "scheduled_at", type: "string", format: "datetime", example: "10/10/2025 10:40:30"),
            new OA\Property(property: "status", type: "string", example: "pending"),
            new OA\Property(property: "total_price", type: "string", format: "decimal", example: "150.50"),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "user", ref: "#/components/schemas/UserResource"),
            new OA\Property(
                property: "services",
                type: "array",
                items: new OA\Items(ref: "#/components/schemas/ServicesResource")
            )
        ]
    )
]
class AppointmentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'scheduled_at'  => $this->scheduled_at?->format('d/m/Y H:i:s'),
            'status'        => $this->status,
            'total_price'   => $this->total_price,
            'user_id'       => $this->user_id,
            'user'          => new UserResource($this->whenLoaded('user')),
            'services'       => ServicesResource::collection($this->whenLoaded('services')),
        ];
    }
}
