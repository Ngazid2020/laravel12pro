<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingEnrollmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'status'      => $this->status,
            'attended_at' => $this->attended_at?->toISOString(),
            'rating'      => $this->rating,
            'comment'     => $this->comment,
            'session'     => new TrainingSessionResource($this->whenLoaded('trainingSession')),
        ];
    }
}