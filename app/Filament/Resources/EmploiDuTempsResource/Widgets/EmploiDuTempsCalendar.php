<?php

namespace App\Filament\Widgets;

use App\Models\EmploiDuTemps;
use App\Models\ClassRoom;
use Filament\Widgets\Widget;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

class EmploiDuTempsCalendar extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.emploi-du-temps-calendar';
    protected int | string | array $columnSpan = 'full';

    public $selectedClassRoom = null;
    public $selectedWeek = null;

    public function mount(): void
    {
        $this->selectedClassRoom = ClassRoom::first()?->id;
        $this->selectedWeek = now()->startOfWeek()->format('Y-m-d');
    }

    public function getFormSchema(): array
    {
        return [
            Select::make('selectedClassRoom')
                ->label('Classe')
                ->options(ClassRoom::pluck('nom', 'id'))
                ->reactive()
                ->afterStateUpdated(fn () => $this->refreshCalendar()),
        ];
    }

    public function refreshCalendar()
    {
        $this->dispatch('refreshCalendar');
    }

    public function getEmploiDuTemps()
    {
        if (!$this->selectedClassRoom) return collect();

        return EmploiDuTemps::with(['cours',  'salle'])
            ->where('class_room_id', $this->selectedClassRoom)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('jour');
    }

    public function getJours()
    {
        return ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    }

    public function getHeures()
    {
        return [
            '08:00', '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00', '17:00', '18:00'
        ];
    }
    public function getViewData(): array
{
    return [
        'selectedClassRoom' => $this->selectedClassRoom,
        'emploiDuTemps' => $this->getEmploiDuTemps(),
        'jours' => $this->getJours(),
        'heures' => $this->getHeures(),
    ];
}

}
