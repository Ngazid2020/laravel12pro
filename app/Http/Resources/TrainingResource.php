<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'description'   => $this->description,
            'prerequisites' => $this->prerequisites,
            'format'        => $this->format,
            'format_label'  => match ($this->format) {
                'in_person' => 'Présentiel',
                'online'    => 'En ligne',
                'hybrid'    => 'Hybride',
                default     => $this->format,
            },
            'capacity'      => $this->capacity,
            'price_type'    => $this->price_type,
            'price'         => $this->price,
            'trainer'       => new UserResource($this->whenLoaded('trainer')),
            'sessions'      => TrainingSessionResource::collection($this->whenLoaded('sessions')),
        ];
    }
}