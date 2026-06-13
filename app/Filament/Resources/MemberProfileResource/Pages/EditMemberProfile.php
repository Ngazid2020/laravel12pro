<?php

namespace App\Filament\Resources\MemberProfileResource\Pages;

use App\Filament\Resources\MemberProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMemberProfile extends EditRecord
{
    protected static string $resource = MemberProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
