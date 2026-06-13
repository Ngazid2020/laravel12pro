<?php

namespace App\Filament\Resources\CandidatureApplicationResource\Pages;

use App\Filament\Resources\CandidatureApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCandidatureApplication extends EditRecord
{
    protected static string $resource = CandidatureApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
