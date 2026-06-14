<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'description'     => $this->description,
            'type'            => $this->type,
            'type_label'      => match ($this->type) {
                'networking'  => 'Networking',
                'conference'  => 'Conférence',
                'masterclass' => 'Masterclass',
                'workshop'    => 'Atelier',
                default       => $this->type,
            },
            'starts_at'       => $this->starts_at?->toISOString(),
            'ends_at'         => $this->ends_at?->toISOString(),
            'location'        => $this->location,
            'capacity'        => $this->capacity,
            'is_full'         => $this->isFull(),
            'spots_left'      => $this->capacity ? max(0, $this->capacity - $this->registrations()->count()) : null,
            'is_paid'         => $this->is_paid,
            'price'           => $this->price,
            'organizer'       => new UserResource($this->whenLoaded('organizer')),
            'is_registered'   => $user ? $this->registrations()->where('user_id', $user->id)->exists() : false,
            'registration_id' => $user ? optional($this->registrations()->where('user_id', $user->id)->first())->id : null,
        ];
    }
}