<?php

namespace App\Filament\Resources\PersonnelSecuriteResource\Pages;

use App\Filament\Resources\PersonnelSecuriteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPersonnelSecurite extends EditRecord
{
    protected static string $resource = PersonnelSecuriteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
