<?php

namespace App\Filament\Resources\FeeResource\Pages;

use App\Filament\Resources\FeeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFee extends CreateRecord
{
    protected static string $resource = FeeResource::class;
    public function getTitle(): string
    {
        return 'Créer Frais'; // ou juste 'Frais' si tu veux
    }
}
