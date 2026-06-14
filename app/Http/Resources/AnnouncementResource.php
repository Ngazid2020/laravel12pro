<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'content'         => $this->content,
            'target_audience' => $this->target_audience,
            'published_at'    => $this->published_at?->toISOString(),
            'expires_at'      => $this->expires_at?->toISOString(),
            'publisher'       => new UserResource($this->whenLoaded('publisher')),
        ];
    }
}