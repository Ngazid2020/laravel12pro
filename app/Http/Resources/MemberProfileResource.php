<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberProfileResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'company_name'          => $this->company_name,
            'project_name'          => $this->project_name,
            'sector'                => $this->sector,
            'city'                  => $this->city,
            'bio'                   => $this->bio,
            'skills_offered'        => $this->skills_offered ?? [],
            'needs_expressed'       => $this->needs_expressed ?? [],
            'social_links'          => $this->social_links ?? [],
            'referral_code'         => $this->referral_code,
            'membership_status'     => $this->membership_status,
            'status_label'          => $this->statusLabel(),
            'status_color'          => $this->statusColor(),
            'membership_expires_at' => $this->membership_expires_at?->toDateString(),
            'activated_at'          => $this->activated_at?->toISOString(),
            'mentor'                => new UserResource($this->whenLoaded('mentor')),
        ];
    }
}