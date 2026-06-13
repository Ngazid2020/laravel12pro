<?php

namespace App\Filament\Resources\MemberProfileResource\Pages;

use App\Filament\Resources\MemberProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateMemberProfile extends CreateRecord
{
    protected static string $resource = MemberProfileResource::class;
}
