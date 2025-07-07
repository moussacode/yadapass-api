<?php

namespace App\Filament\Resources\CarteEtudianteResource\Pages;

use App\Filament\Resources\CarteEtudianteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarteEtudiantes extends ListRecords
{
    protected static string $resource = CarteEtudianteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
