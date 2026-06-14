<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TrainingSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'           => $this->id,
            'training_id'  => $this->training_id,
            'starts_at'    => $this->starts_at?->toISOString(),
            'ends_at'      => $this->ends_at?->toISOString(),
            'location'     => $this->location,
            'meeting_link' => $this->meeting_link,
            'status'       => $this->status,
            'materials'    => $this->materials ?? [],
            'is_full'      => $this->isFull(),
            'spots_left'   => $this->spotsLeft(),
            'is_enrolled'  => $user ? $this->enrollments()->where('user_id', $user->id)->exists() : false,
            'training'     => new TrainingResource($this->whenLoaded('training')),
        ];
    }
}