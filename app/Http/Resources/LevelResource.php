<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'slug'                 => $this->slug,
            'description'          => $this->description,
            'badge_color'          => $this->badge_color,
            'min_points'           => $this->min_points,
            'required_trainings'   => $this->required_trainings,
            'required_months'      => $this->required_months,
            'grants_mentor_status' => $this->grants_mentor_status,
            'order'                => $this->order,
            'rewards'              => $this->whenLoaded('rewards', fn () => $this->rewards->map(fn ($r) => [
                'type'        => $r->type,
                'description' => $r->description,
                'value'       => $r->value,
            ])),
        ];
    }
}