{{-- resources/views/filament/resources/emploi-du-temps-resource/pages/calendar-emploi-du-temps.blade.php --}}
<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filtre de classe --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Filtrer par classe
                </h2>
            </div>

            <form wire:submit="$refresh" class="flex items-end gap-4">
                {{ $this->form }}
                <x-filament::button type="submit" icon="heroicon-o-arrow-path">
                    Actualiser
                </x-filament::button>
            </form>
        </div>

        @if($selectedClassRoom)
        {{-- Navigation du calendrier --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 no-print-nav">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-4">
                    {{-- Sélecteur de vue --}}
                    <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                        <button wire:click="setView('day')"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                                           @if($currentView === 'day') bg-white dark:bg-gray-600 shadow-sm @else text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 @endif">
                            Jour
                        </button>
                        <button wire:click="setView('week')"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                                           @if($currentView === 'week') bg-white dark:bg-gray-600 shadow-sm @else text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 @endif">
                            Semaine
                        </button>
                        <button wire:click="setView('month')"
                            class="px-3 py-2 text-sm font-medium rounded-md transition-colors
                                           @if($currentView === 'month') bg-white dark:bg-gray-600 shadow-sm @else text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 @endif">
                            Mois
                        </button>
                    </div>

                    {{-- Bouton Aujourd'hui --}}
                    <x-filament::button
                        wire:click="goToToday"
                        size="sm"
                        color="gray"
                        icon="heroicon-o-calendar-days">
                        Aujourd'hui
                    </x-filament::button>
                </div>

                {{-- Navigation période --}}
                <div class="flex items-center gap-2">
                    <x-filament::button
                        wire:click="previousPeriod"
                        size="sm"
                        icon="heroicon-o-chevron-left"
                        color="gray">
                    </x-filament::button>

                    <div class="px-4 py-2 text-lg font-semibold text-gray-900 dark:text-white min-w-[200px] text-center">
                        {{ $this->getCurrentPeriodLabel() }}
                    </div>

                    <x-filament::button
                        wire:click="nextPeriod"
                        size="sm"
                        icon="heroicon-o-chevron-right"
                        color="gray">
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- En-tête du calendrier --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Emploi du temps
                    </h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Classe : <span class="font-semibold">{{ $this->getSelectedClasse()?->name }}</span>
                    </p>
                </div>
                <div class="flex gap-2">
                    <x-filament::button
                        tag="a"
        href="{{ route('filament.admin.resources.emploi-du-temps.create') }}"
        icon="heroicon-o-plus"
                        size="sm">
                        Ajouter un cours
                    </x-filament::button>

                    <x-filament::button
                        color="success"
                        icon="heroicon-o-printer"
                        onclick="window.print()"
                        size="sm">
                        Imprimer
                    </x-filament::button>
                </div>
            </div>
        </div>

        {{-- Calendrier --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider w-20 border-r-2 border-gray-200 dark:border-gray-600">
                                Heures
                            </th>
                            @foreach($this->getCurrentPeriodDays() as $day)
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider border-r-2 border-gray-200 dark:border-gray-600 last:border-r-0">
                                <div class="space-y-1">
                                    <div class="font-semibold">{{ $day['name'] }}</div>
                                    <div class="text-sm text-gray-400 dark:text-gray-500">{{ $day['date'] }}</div>
                                </div>
                            </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800">
                        @foreach($this->getTimeSlots() as $timeSlot)
                        <tr class="border-b border-gray-200 dark:border-gray-700">
                            <td class="px-4 py-2 text-sm font-medium text-gray-900 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border-r-2 border-gray-200 dark:border-gray-600 relative">
                                <div class="flex items-center justify-between">
                                    <div class="text-center">
                                        <div class="font-semibold">{{ $timeSlot['display'] }}</div>
                                    </div>
                                    {{-- Barres visuelles pour les 30 minutes --}}
                                    <div class="flex flex-col gap-1">
                                        <div class="w-3 h-1 bg-gray-300 dark:bg-gray-950 rounded-full"></div>
                                        <div class="w-3 h-1 bg-gray-300 dark:bg-gray-950 rounded-full"></div>
                                    </div>
                                </div>
                            </td>
                            @foreach($this->getCurrentPeriodDays() as $day)
                            <td class="px-2 py-1 text-sm text-gray-500 dark:text-gray-400 border-r-2 border-gray-200 dark:border-gray-600 last:border-r-0 h-16 align-top relative">
                                @php
                                $emploiDuTemps = $this->getEmploiDuTemps();
                                $coursJour = $emploiDuTemps->get($day['day_name'], collect());
                                $coursActuel = $this->getCoursForTimeSlot($coursJour, $timeSlot);
                                @endphp

                                @if($coursActuel && !$coursActuel['continued'])
                                <div class="absolute inset-1 bg-green-100 dark:bg-green-900 rounded-lg border-l-4 border-green-500 overflow-hidden cours-card">
                                    <div class="p-2 h-full flex flex-col justify-between">
                                        <div>
                                            <div class="font-semibold text-green-800 dark:text-green-200 text-xs leading-tight">
                                                {{ $coursActuel['cours']->cours->nom ?? 'Cours' }}
                                            </div>
                                            <div class="text-green-600 dark:text-green-300 text-xs mt-1">
                                                {{ $coursActuel['cours']->cours->enseignant ?? 'Professeur' }}
                                            </div>
                                        </div>
                                        <div class="text-green-500 dark:text-green-400 text-xs">
                                            <div>
                                                {{ \Carbon\Carbon::parse($coursActuel['cours']->heure_debut)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($coursActuel['cours']->heure_fin)->format('H:i') }}
                                            </div>
                                            @if(optional($coursActuel['cours']->salle)->nom)
                                            <div class="flex items-center gap-1 mt-1">
                                                <span>📍</span>
                                                <span>{{ $coursActuel['cours']->salle->nom }}</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Statistiques --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Statistiques de la {{ $currentView === 'week' ? 'semaine' : ($currentView === 'month' ? 'mois' : 'journée') }}
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @php
                $stats = $this->getPeriodStats();
                @endphp

                <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                    <div class="text-blue-600 dark:text-blue-300 text-sm font-medium">
                        Total des cours
                    </div>
                    <div class="text-2xl font-bold text-blue-700 dark:text-blue-200">
                        {{ $stats['total_cours'] }}
                    </div>
                </div>

                <div class="bg-green-50 dark:bg-green-900 rounded-lg p-4">
                    <div class="text-green-600 dark:text-green-300 text-sm font-medium">
                        Heures de cours
                    </div>
                    <div class="text-2xl font-bold text-green-700 dark:text-green-200">
                        {{ $stats['total_heures'] }}h
                    </div>
                </div>

                <div class="bg-yellow-50 dark:bg-yellow-900 rounded-lg p-4">
                    <div class="text-yellow-600 dark:text-yellow-300 text-sm font-medium">
                        Professeurs différents
                    </div>
                    <div class="text-2xl font-bold text-yellow-700 dark:text-yellow-200">
                        {{ $stats['nb_professeurs'] }}
                    </div>
                </div>

                <div class="bg-purple-50 dark:bg-purple-900 rounded-lg p-4">
                    <div class="text-purple-600 dark:text-purple-300 text-sm font-medium">
                        Salles utilisées
                    </div>
                    <div class="text-2xl font-bold text-purple-700 dark:text-purple-200">
                        {{ $stats['nb_salles'] }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Légende --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                Légende
            </h3>
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 bg-green-100 dark:bg-green-900 border-l-4 border-green-500 rounded"></div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Cours programmé</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex flex-col gap-1">
                        <div class="w-4 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                        <div class="w-4 h-1 bg-gray-300 dark:bg-gray-600 rounded-full"></div>
                    </div>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Créneaux de 30 minutes</span>
                </div>
            </div>
        </div>
        @else
        {{-- Message si aucune classe sélectionnée --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center">
            <div class="text-gray-400 dark:text-gray-500 mb-4">
                <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                Aucune classe sélectionnée
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
                Veuillez sélectionner une classe ci-dessus pour voir l'emploi du temps
            </p>
        </div>
        @endif
    </div>

    {{-- Styles pour l'impression et animations --}}
    <style>
        @media print {
            .no-print,
            .no-print-nav {
                display: none !important;
            }

            body {
                margin: 0;
            }

            .bg-white {
                background: white !important;
            }

            .text-gray-900 {
                color: black !important;
            }

            .border-gray-200 {
                border-color: #ccc !important;
            }
        }

        /* Animation pour les cours */
        .cours-card {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .cours-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        /* Amélioration des bordures */
        .border-r-2 {
            border-right-width: 2px;
        }

        /* Styles pour les créneaux horaires */
        .time-slot {
            position: relative;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .no-print-nav .flex {
                flex-direction: column;
                gap: 1rem;
            }

            .no-print-nav [class*="min-w-"] {
                min-width: 100%;
            }
        }
    </style>
</x-filament-panels::page>