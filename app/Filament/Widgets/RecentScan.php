<?php

namespace App\Filament\Widgets;

use App\Models\Scan;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentScan extends BaseWidget
{
    protected static ?int $sort = 2; // Ordre d'affichage du widget (plus petit = plus haut)
    
    // Méthode pour définir la largeur du widget
    protected int | string | array $columnSpan = 'full'; // Prend toute la largeur
    
    // Alternative: vous pouvez aussi utiliser cette méthode
    public static function getColumns(): int | string | array
    {
        return 'full'; // Prend toute la largeur disponible
    }
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Scan::query()
                    ->latest()
                    ->take(10) // Augmenté à 10 pour mieux utiliser l'espace
            )
            ->columns([
                Tables\Columns\TextColumn::make('matricule')
                    ->label('Matricule')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('date_heure')
                    ->label('Date & Heure')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\IconColumn::make('validation')
                    ->label('Validé')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\BadgeColumn::make('statut_acces')
                    ->colors([
                        'success' => 'accepte',
                        'danger' => 'refuse',
                        'warning' => 'en_attente',
                    ])
                    ->label('Statut d\'accès'),
            ])
            ->striped() // Ajoute des rayures alternées
            ->paginated(false) // Désactive la pagination pour un widget
            ->defaultSort('date_heure', 'desc'); // Tri par défaut
    }
}