<?php

namespace App\Filament\Resources\PersonnelSecuriteResource\Pages;

use App\Filament\Resources\PersonnelSecuriteResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePersonnelSecurite extends CreateRecord
{
    protected static string $resource = PersonnelSecuriteResource::class;
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
