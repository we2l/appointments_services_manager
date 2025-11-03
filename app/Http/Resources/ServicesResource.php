<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: "ServicesResource",
    description: "Recurso que representa um Serviço",
    properties: [
        new OA\Property(property: "id", type: "integer", example: 1),
        new OA\Property(property: "name", type: "string", example: "Corte de cabelo"),
        new OA\Property(property: "price", type: "number", format: "float", example: 50.0),
    ]
)]
class ServicesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
          'id'      => $this->id,
          'name'    => $this->name,
          'price'   => $this->price,
        ];
    }
}
