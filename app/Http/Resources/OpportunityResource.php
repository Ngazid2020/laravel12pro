<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OpportunityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $request->user();

        return [
            'id'              => $this->id,
            'title'           => $this->title,
            'description'     => $this->description,
            'type'            => $this->type,
            'sector'          => $this->sector,
            'target_skills'   => $this->target_skills ?? [],
            'deadline'        => $this->deadline?->toDateString(),
            'partner_company' => $this->whenLoaded('partnerCompany', fn () => [
                'id'   => $this->partnerCompany->id,
                'name' => $this->partnerCompany->name,
                'logo' => $this->partnerCompany->logo ? asset('storage/'.$this->partnerCompany->logo) : null,
            ]),
            'has_applied'     => $user ? $this->applications()->where('user_id', $user->id)->exists() : false,
            'created_at'      => $this->created_at?->toISOString(),
        ];
    }
}