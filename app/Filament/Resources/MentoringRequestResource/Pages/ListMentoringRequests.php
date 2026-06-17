<?php

namespace App\Filament\Resources\MentoringRequestResource\Pages;

use App\Filament\Resources\MentoringRequestResource;
use Filament\Resources\Pages\ListRecords;

class ListMentoringRequests extends ListRecords
{
    protected static string $resource = MentoringRequestResource::class;

    protected function getHeaderActions(): array
    {
        return []; // Les demandes sont créées par les membres, pas par l'admin
    }
}
