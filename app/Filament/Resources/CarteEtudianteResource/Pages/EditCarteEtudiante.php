<?php

namespace App\Filament\Resources\CarteEtudianteResource\Pages;

use App\Filament\Resources\CarteEtudianteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCarteEtudiante extends EditRecord
{
    protected static string $resource = CarteEtudianteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Prévisualiser')
                ->icon('heroicon-o-eye')
                ->color('info')
                ->url(fn (): string => route('carte.preview', $this->record))
                ->openUrlInNewTab(),

            Actions\Action::make('print')
                ->label('Imprimer')
                ->icon('heroicon-o-printer')
                ->color('success')
                ->url(fn (): string => route('carte.print', $this->record))
                ->openUrlInNewTab(),

            Actions\DeleteAction::make(),
        ];
    }
}
