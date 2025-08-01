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
            
            Actions\Action::make('nouveau_paiement')
                ->label('Consulter le calendrier')
                ->icon('heroicon-o-plus-circle')
                ->color('primary')
                ->url('/admin/emploi-du-temps/calendar')
                ->openUrlInNewTab(false),
        ];
    }
}
