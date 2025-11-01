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
            'title'         => $this->title,
            'scheduled_at'  => $this->scheduled_at->format('d/m/Y H:i:s'),
            'status'        => $this->status,
            'user_id'       => $this->user_id
        ];
    }
}
