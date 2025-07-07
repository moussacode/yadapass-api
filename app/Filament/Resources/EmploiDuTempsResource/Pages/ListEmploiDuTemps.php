<?php

namespace App\Filament\Resources\EmploiDuTempsResource\Pages;

use App\Filament\Resources\EmploiDuTempsResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmploiDuTemps extends ListRecords
{
    protected static string $resource = EmploiDuTempsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
