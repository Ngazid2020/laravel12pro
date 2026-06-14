<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContactRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'status'     => $this->status,
            'message'    => $this->message,
            'sender'     => new UserResource($this->whenLoaded('sender')),
            'receiver'   => new UserResource($this->whenLoaded('receiver')),
            'created_at' => $this->created_at?->toISOString(),
        ];
    }
}