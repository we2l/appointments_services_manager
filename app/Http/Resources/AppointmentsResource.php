<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
