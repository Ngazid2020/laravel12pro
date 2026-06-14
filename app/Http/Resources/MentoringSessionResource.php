<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MentoringSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'scheduled_at'        => $this->scheduled_at?->toISOString(),
            'held_at'             => $this->held_at?->toISOString(),
            'status'              => $this->status,
            'notes'               => $this->notes,
            'confirmed_by_mentee' => $this->confirmed_by_mentee,
            'mentor'              => new UserResource($this->whenLoaded('mentor')),
            'mentee'              => new UserResource($this->whenLoaded('mentee')),
            'created_at'          => $this->created_at?->toISOString(),
        ];
    }
}