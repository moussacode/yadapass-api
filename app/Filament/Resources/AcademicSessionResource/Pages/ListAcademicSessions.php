<?php

namespace App\Filament\Resources\AcademicSessionResource\Pages;

use App\Filament\Resources\AcademicSessionResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAcademicSessions extends ListRecords
{
    protected static string $resource = AcademicSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
             Action::make('activer')
                ->label('Définir comme année active')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Confirmer l\'activation')
                ->modalDescription('Cela rendra cette année active et désactivera toutes les autres. Êtes-vous sûr ?')
                ->modalSubmitActionLabel('Oui, activer')
                ->action(function ($record) {
                    // désactiver toutes les autres
                    \App\Models\AcademicSession::where('id', '!=', $record->id)
                        ->update(['actif' => false]);

                    // activer celle-là
                    $record->update(['actif' => true]);

                    Notification::make()
                        ->title('Année activée')
                        ->body("L’année {$record->nom} est maintenant la seule active.")
                        ->success()
                        ->send();
                }),
        ];
    }
}
