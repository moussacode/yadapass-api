<?php

// app/Filament/Resources/EmploiDuTempsResource/Pages/CalendarEmploiDuTemps.php
namespace App\Filament\Resources\EmploiDuTempsResource\Pages;

use App\Filament\Resources\EmploiDuTempsResource;
use App\Models\EmploiDuTemps;
use App\Models\ClassRoom;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class CalendarEmploiDuTemps extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $resource = EmploiDuTempsResource::class;
    protected static string $view = 'filament.resources.emploi-du-temps-resource.pages.calendar-emploi-du-temps';
    protected static ?string $navigationLabel = 'Calendrier';
    protected static ?string $title = 'Emploi du temps - Calendrier';

    public $selectedClassRoom = null;
    public string $currentView = 'week'; // 'day', 'week', 'month'
    public Carbon $currentDate;

    public function mount(): void
    {
        $this->currentDate = Carbon::now();
        $this->selectedClassRoom = ClassRoom::first()?->id;
        $this->form->fill([
            'selectedClassRoom' => $this->selectedClassRoom,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedClassRoom')
                    ->label('Sélectionner une classe')
                    ->options(ClassRoom::pluck('name', 'id'))
                    ->reactive()
                    ->afterStateUpdated(function ($state) {
                        $this->selectedClassRoom = $state;
                    }),
            ]);
    }

    // Méthodes de navigation
    public function setView(string $view): void
    {
        $this->currentView = $view;
    }

    public function goToToday(): void
    {
        $this->currentDate = Carbon::now();
    }

    public function previousPeriod(): void
    {
        switch ($this->currentView) {
            case 'day':
                $this->currentDate->subDay();
                break;
            case 'week':
                $this->currentDate->subWeek();
                break;
            case 'month':
                $this->currentDate->subMonth();
                break;
        }
    }

    public function nextPeriod(): void
    {
        switch ($this->currentView) {
            case 'day':
                $this->currentDate->addDay();
                break;
            case 'week':
                $this->currentDate->addWeek();
                break;
            case 'month':
                $this->currentDate->addMonth();
                break;
        }
    }

    public function getCurrentPeriodLabel(): string
    {
        switch ($this->currentView) {
            case 'day':
                return $this->currentDate->locale('fr')->format('l j F Y');
            case 'week':
                $startOfWeek = $this->currentDate->copy()->startOfWeek();
                $endOfWeek = $this->currentDate->copy()->endOfWeek();
                return $startOfWeek->format('j M') . ' - ' . $endOfWeek->format('j M Y');
            case 'month':
                return $this->currentDate->locale('fr')->format('F Y');
            default:
                return '';
        }
    }

    public function getCurrentPeriodDays(): array
    {
        switch ($this->currentView) {
            case 'day':
                return [
                    [
                        'name' => $this->getJourFrancais($this->currentDate->format('l')),
                        'date' => $this->currentDate->format('j/m'),
                        'key' => $this->currentDate->format('Y-m-d'),
                        'carbon' => $this->currentDate->copy(),
                        'day_name' => $this->getJourFrancais($this->currentDate->format('l')),
                    ]
                ];
            case 'week':
                $days = [];
                $startOfWeek = $this->currentDate->copy()->startOfWeek();
                for ($i = 0; $i < 6; $i++) { // Seulement du lundi au samedi
                    $day = $startOfWeek->copy()->addDays($i);
                    $days[] = [
                        'name' => $this->getJourFrancais($day->format('l')),
                        'date' => $day->format('j/m'),
                        'key' => $day->format('Y-m-d'),
                        'carbon' => $day,
                        'day_name' => $this->getJourFrancais($day->format('l'))
                    ];
                }
                return $days;
            case 'month':
                $days = [];
                $startOfMonth = $this->currentDate->copy()->startOfMonth();
                $endOfMonth = $this->currentDate->copy()->endOfMonth();
                
                $current = $startOfMonth->copy();
                while ($current <= $endOfMonth) {
                    if ($current->dayOfWeek !== 0) { // Exclure le dimanche
                        $days[] = [
                            'name' => $this->getJourFrancais($current->format('l')),
                            'date' => $current->format('j/m'),
                            'key' => $current->format('Y-m-d'),
                            'carbon' => $current->copy(),
                            'day_name' => $this->getJourFrancais($current->format('l'))
                        ];
                    }
                    $current->addDay();
                }
                return $days;
        }
        return [];
    }

    private function getJourFrancais(string $englishDay): string
    {
        $days = [
            'Monday' => 'Lundi',
            'Tuesday' => 'Mardi',
            'Wednesday' => 'Mercredi',
            'Thursday' => 'Jeudi',
            'Friday' => 'Vendredi',
            'Saturday' => 'Samedi',
            'Sunday' => 'Dimanche'
        ];
        
        return $days[$englishDay] ?? $englishDay;
    }

    public function getTimeSlots(): array
    {
        $slots = [];
        // Créneaux de 30 minutes de 8h à 21h
        for ($hour = 8; $hour <= 21; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $slots[] = [
                    'time' => $time,
                    'display' => $time,
                    'minutes' => $hour * 60 + $minute
                ];
            }
        }
        return $slots;
    }

    public function getEmploiDuTemps(): Collection
    {
        if (!$this->selectedClassRoom) {
            return collect();
        }

        $emplois = EmploiDuTemps::with(['cours'])
            ->where('class_room_id', $this->selectedClassRoom)
            ->orderBy('jour')
            ->orderBy('heure_debut')
            ->get();

        return $emplois->groupBy('jour');
    }

    public function getCoursForTimeSlot(Collection $coursJour, array $timeSlot): ?array
{
    foreach ($coursJour as $cours) {
        $heureDebut = Carbon::parse($cours->heure_debut);
        $heureFin = Carbon::parse($cours->heure_fin);
        $slotTime = Carbon::parse($timeSlot['time']);

        // Le slotTime est dans l'intervalle du cours
        if ($slotTime->between($heureDebut, $heureFin->subMinute())) {
            $durationMinutes = $heureFin->diffInMinutes($heureDebut);
            $rowspan = intval($durationMinutes / 30);

            $isStart = $slotTime->eq($heureDebut);

            return [
                'cours' => $cours,
                'continued' => !$isStart,
                'rowspan' => $rowspan,
            ];
        }
    }

    return null;
}


    public function getPeriodStats(): array
    {
        if (!$this->selectedClassRoom) {
            return [
                'total_cours' => 0,
                'total_heures' => 0,
                'nb_professeurs' => 0,
                'nb_salles' => 0
            ];
        }

        $emplois = $this->getEmploiDuTemps()->flatten();
        
        $totalCours = $emplois->count();
        $totalHeures = $emplois->sum(function ($cours) {
            return Carbon::parse($cours->heure_debut)->diffInHours(Carbon::parse($cours->heure_fin));
        });
        
        $professeurs = $emplois->pluck('cours.enseignant')->unique()->filter()->count();
        $salles = $emplois->pluck('salle')->unique()->filter()->count();

        return [
            'total_cours' => $totalCours,
            'total_heures' => $totalHeures,
            'nb_professeurs' => $professeurs,
            'nb_salles' => $salles
        ];
    }

    public function getSelectedClasse()
    {
        return ClassRoom::find($this->selectedClassRoom);
    }

    public function getJours()
    {
        return ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'];
    }

    public function getHeures()
    {
        return [
            '08:00', '09:00', '10:00', '11:00', '12:00',
            '13:00', '14:00', '15:00', '16:00', '17:00', 
            '18:00', '19:00', '20:00', '21:00'
        ];
    }

    public function exportPDF()
    {
        // Logique d'export PDF (à implémenter plus tard)
        $this->notify('success', 'Export PDF en cours de développement');
    }

    // Méthodes d'aide pour la compatibilité
    public function getCoursForDay(string $jour, string $heure): ?EmploiDuTemps
    {
        $emplois = $this->getEmploiDuTemps();
        $coursJour = $emplois->get($jour, collect());
        
        
        return $coursJour->first(function ($item) use ($heure) {
            $heureDebut = Carbon::parse($item->heure_debut)->format('H:i');
            $heureFin = Carbon::parse($item->heure_fin)->format('H:i');
            return $heure >= $heureDebut && $heure < $heureFin;
        });
    }

    
    public function getCoursSpan(EmploiDuTemps $cours): int
    {
        $heureDebut = Carbon::parse($cours->heure_debut);
        $heureFin = Carbon::parse($cours->heure_fin);
        return $heureFin->diffInMinutes($heureDebut) / 30; // Nombre de créneaux de 30 minutes
    }

    public function isCoursStart(EmploiDuTemps $cours, string $heure): bool
    {
        $heureDebut = Carbon::parse($cours->heure_debut)->format('H:i');
        return $heure === $heureDebut;
    }
}