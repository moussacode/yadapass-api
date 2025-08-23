{{-- resources/views/filament/infolists/etudiant-finances.blade.php --}}
@php
    $etudiant = $getRecord();
    $attribution = App\Models\Attribution::where('etudiant_id', $etudiant->id)->latest()->first();
    
    $fraisDisponibles = collect();
    $situationFinanciere = collect();
    
    if($attribution && $attribution->classRoom) {
        $fraisDisponibles = App\Models\Fee::where('class_room_id', $attribution->classRoom->id)->get();
        
        foreach($fraisDisponibles as $frais) {
            $totalPaye = App\Models\Paiement::where('etudiant_id', $etudiant->id)
                ->where('fee_id', $frais->id)
                ->sum('montant');
            
            $reste = $frais->montant_total - $totalPaye;
            $pourcentage = $frais->montant_total > 0 ? ($totalPaye / $frais->montant_total) * 100 : 0;
            
            $situationFinanciere->push([
                'frais' => $frais,
                'total_paye' => $totalPaye,
                'reste' => $reste,
                'pourcentage' => $pourcentage,
                'status' => $reste <= 0 ? 'Soldé' : ($totalPaye > 0 ? 'Partiellement payé' : 'Non payé')
            ]);
        }
    }
    
    $totalGeneral = $situationFinanciere->sum('frais.montant_total');
    $totalPayeGeneral = $situationFinanciere->sum('total_paye');
    $resteGeneral = $totalGeneral - $totalPayeGeneral;
    $pourcentageGeneral = $totalGeneral > 0 ? ($totalPayeGeneral / $totalGeneral) * 100 : 0;
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    @if($situationFinanciere->isNotEmpty())
        {{-- Vue d'ensemble --}}
        <div style="margin-bottom: 2rem; border: 1px solid #bbf7d0; border-radius: 1rem; padding: 1.5rem; background: linear-gradient(to right, #f0fdf4, #ecfdf5);">
            <h3 style="display: flex; align-items: center; font-size: 1.125rem; font-weight: 600; color: #111827; margin-bottom: 1rem;">
                <x-heroicon-o-chart-pie style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; color: #16a34a;" />
                Vue d'ensemble financière
            </h3>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                <div style="background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #111827;">{{ number_format($totalGeneral, 0, ',', ' ') }}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Total à payer (FCFA)</div>
                </div>

                <div style="background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #16a34a;">{{ number_format($totalPayeGeneral, 0, ',', ' ') }}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Total payé (FCFA)</div>
                </div>

                <div style="background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #dc2626;">{{ number_format($resteGeneral, 0, ',', ' ') }}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Reste à payer (FCFA)</div>
                </div>

                <div style="background: white; padding: 1rem; border-radius: 0.5rem; border: 1px solid #e5e7eb; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: bold; color: #2563eb;">{{ number_format($pourcentageGeneral, 1) }}%</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">Progression</div>
                </div>
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;">
                    <span>Progression générale</span>
                    <span>{{ number_format($pourcentageGeneral, 1) }}%</span>
                </div>
                <div style="width: 100%; background-color: #e5e7eb; border-radius: 9999px; height: 12px;">
                    <div style="height: 12px; border-radius: 9999px; background: linear-gradient(to right, #22c55e, #16a34a); width: {{ $pourcentageGeneral }}%; transition: width 0.5s ease-out;"></div>
                </div>
            </div>
        </div>

        {{-- Détail par frais --}}
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827; display: flex; align-items: center;">
                <x-heroicon-o-list-bullet style="width: 1.25rem; height: 1.25rem; margin-right: 0.5rem; color: #2563eb;" />
                Détail par frais
            </h3>
            
            @foreach($situationFinanciere as $situation)
                @php
                    $statusStyles = match($situation['status']) {
                        'Soldé' => [
                            'bg' => '#dcfce7',
                            'text' => '#166534',
                            'progress' => '#22c55e'
                        ],
                        'Partiellement payé' => [
                            'bg' => '#fef3c7',
                            'text' => '#92400e',
                            'progress' => '#eab308'
                        ],
                        default => [
                            'bg' => '#fee2e2',
                            'text' => '#991b1b',
                            'progress' => '#ef4444'
                        ]
                    };
                @endphp
                
                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; transition: box-shadow 0.15s ease-in-out;" 
                     onmouseover="this.style.boxShadow='0 4px 6px -1px rgba(0, 0, 0, 0.1)'" 
                     onmouseout="this.style.boxShadow='none'">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.75rem;">
                        <h4 style="font-weight: 600; color: #111827;">{{ $situation['frais']->nom }}</h4>
                        <span style="display: inline-flex; align-items: center; padding: 0.125rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500; background-color: {{ $statusStyles['bg'] }}; color: {{ $statusStyles['text'] }};">
                            {{ $situation['status'] }}
                        </span>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; font-size: 0.875rem; margin-bottom: 0.75rem;">
                        <div>
                            <span style="color: #6b7280;">Total :</span>
                            <span style="font-weight: 500;">{{ number_format($situation['frais']->montant_total, 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div>
                            <span style="color: #6b7280;">Payé :</span>
                            <span style="font-weight: 500; color: #16a34a;">{{ number_format($situation['total_paye'], 0, ',', ' ') }} FCFA</span>
                        </div>
                        <div>
                            <span style="color: #6b7280;">Reste :</span>
                            <span style="font-weight: 500; color: #dc2626;">{{ number_format($situation['reste'], 0, ',', ' ') }} FCFA</span>
                        </div>
                    </div>
                    
                    {{-- Barre de progression --}}
                    <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280;">
                            <span>Progression</span>
                            <span>{{ number_format($situation['pourcentage'], 1) }}%</span>
                        </div>
                        <div style="width: 100%; background-color: #e5e7eb; border-radius: 9999px; height: 0.5rem;">
                            <div style="height: 0.5rem; border-radius: 9999px; background-color: {{ $statusStyles['progress'] }}; width: {{ $situation['pourcentage'] }}%; transition: all 0.3s ease;"></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Actions rapides --}}
        <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
            <a href="/admin/paiements/create?etudiant_id={{ $etudiant->id }}" 
               style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #2563eb; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s ease;" 
               onmouseover="this.style.backgroundColor='#1d4ed8'" 
               onmouseout="this.style.backgroundColor='#2563eb'">
                <x-heroicon-o-plus-circle style="width: 1rem; height: 1rem; margin-right: 0.375rem;" />
                Nouveau paiement
            </a>
            
            <a href="/admin/paiements?tableFilters[etudiant_id][value]={{ $etudiant->id }}" 
               style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #16a34a; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s ease;" 
               onmouseover="this.style.backgroundColor='#15803d'" 
               onmouseout="this.style.backgroundColor='#16a34a'">
                <x-heroicon-o-eye style="width: 1rem; height: 1rem; margin-right: 0.375rem;" />
                Voir tous les paiements
            </a>
        </div>
        
    @else
        <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1.5rem; text-align: center;">
            <div style="display: flex; flex-direction: column; align-items: center; gap: 0.75rem;">
                <div style="background-color: #9ca3af; padding: 0.75rem; border-radius: 9999px;">
                    <x-heroicon-o-currency-dollar style="width: 2rem; height: 2rem; color: white;" />
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Aucun frais disponible</h3>
                    <p style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                        @if(!$attribution)
                            L'étudiant doit être attribué à une classe pour voir les frais.
                        @else
                            Aucun frais n'est configuré pour cette classe.
                        @endif
                    </p>
                </div>
                
                @if(!$attribution)
                    <a href="/admin/attributions/create" 
                       style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; background-color: #2563eb; color: white; font-size: 0.875rem; font-weight: 500; border-radius: 0.5rem; text-decoration: none; transition: background-color 0.15s ease;" 
                       onmouseover="this.style.backgroundColor='#1d4ed8'" 
                       onmouseout="this.style.backgroundColor='#2563eb'">
                        <x-heroicon-o-plus style="width: 1rem; height: 1rem; margin-right: 0.375rem;" />
                        Attribuer à une classe
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>