<?php

namespace App\Filament\Widgets;

use App\Models\ClassRoom;
use Filament\Widgets\ChartWidget;

class StudentsPerClassChart extends ChartWidget
{
    protected static ?string $heading = 'Étudiants par classe';
    protected static string $color = 'info';

 protected function getData(): array
{
    $classes = \App\Models\ClassRoom::all();

    $labels = [];
    $data = [];

    foreach ($classes as $class) {
        $count = \App\Models\Attribution::where('class_room_id', $class->id)->count();
        $labels[] = $class->name;
        $data[] = $count;
    }

    return [
        'datasets' => [
            [
                'label' => 'Nombre d’étudiants',
                'data' => $data,
                'backgroundColor' => '#3B82F6',
            ],
        ],
        'labels' => $labels,
    ];
}


    protected function getType(): string
    {
        return 'bar'; // ou 'pie', 'doughnut'
    }
    protected function getHeight(): string|null
    {
        return '400px'; // Ajustez la hauteur maximale du graphique
    }
}