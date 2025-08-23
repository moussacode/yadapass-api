<?php

namespace App\Filament\Widgets;

use App\Models\Attribution;
use App\Models\Etudiant;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Etudiant',Etudiant::count())
                ->color('primary')
                ->icon('heroicon-o-users'),
            Stat::make('Étudiants inscrits', Attribution::distinct('etudiant_id')->count('etudiant_id'))
    ->color('success')
    ->icon('heroicon-o-user-group')
    ->chart([1,3,2,4,5,6,7,8,9,2,3,4,5,6,7,8,9])

    


            
        ];
    }
}
