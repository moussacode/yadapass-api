<?php

namespace App\Filament\Resources\CarteEtudianteResource\Pages;

use App\Filament\Resources\CarteEtudianteResource;
use Filament\Resources\Pages\Page;

class ViewCarteEtudiante extends Page
{
    protected static string $resource = CarteEtudianteResource::class;

    protected static string $view = 'filament.resources.carte-etudiante-resource.pages.view-carte-etudiante';
}
