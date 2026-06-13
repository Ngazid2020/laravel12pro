<?php

namespace App\Filament\Resources\CandidatureApplicationResource\Pages;

use App\Filament\Resources\CandidatureApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCandidatureApplications extends ListRecords
{
    protected static string $resource = CandidatureApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
