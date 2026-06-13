<?php

namespace App\Filament\Resources\PartnerCompanyResource\Pages;

use App\Filament\Resources\PartnerCompanyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPartnerCompany extends EditRecord
{
    protected static string $resource = PartnerCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
