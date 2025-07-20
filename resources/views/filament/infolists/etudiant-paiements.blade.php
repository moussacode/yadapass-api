{{-- resources/views/filament/infolists/etudiant-paiements.blade.php --}}
@php
    $etudiant = $getRecord();
    $paiementsRecents = App\Models\Paiement::where('etudiant_id', $etudiant->id)
        ->with(['fee', 'admin'])
        ->orderBy('date', 'desc')
        ->take(10)
        ->get();

    $nombreTotalPaiements = App\Models\Paiement::where('etudiant_id', $etudiant->id)->count();
@endphp

<div style="display: flex; flex-direction: column; gap: 1rem;">
    @if($paiementsRecents->isNotEmpty())
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
            <h3 style="font-size: 1.125rem; font-weight: 600; color: #ffffff; display: flex; align-items: center;">
                🕒 Derniers paiements
                <span style="margin-left: 0.5rem; font-size: 0.875rem; font-weight: 400; color: #6b7280;">
                    ({{ $paiementsRecents->count() }} sur {{ $nombreTotalPaiements }})
                </span>
            </h3>

            @if($nombreTotalPaiements > 10)
                <a href="/admin/paiements?tableFilters[etudiant_id][value]={{ $etudiant->id }}"
                   style="font-size: 0.875rem; font-weight: 500; color: #2563eb; text-decoration: none;">
                    Voir tous les paiements →
                </a>
            @endif
        </div>

        <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($paiementsRecents as $paiement)
                @php
                    $totalPayePourCeFrais = App\Models\Paiement::where('etudiant_id', $etudiant->id)
                        ->where('fee_id', $paiement->fee?->id)
                        ->sum('montant');
                    $pourcentageFrais = $paiement->fee && $paiement->fee->montant_total > 0
                        ? ($totalPayePourCeFrais / $paiement->fee->montant_total) * 100
                        : 0;

                    $joursDepuis = $paiement->date->diffInDays(now());
                    $couleurAge = $joursDepuis <= 7 ? 'green' : ($joursDepuis <= 30 ? 'orange' : 'gray');
                @endphp

                <div style="background: white; border: 1px solid #e5e7eb; border-radius: 0.5rem; padding: 1rem; transition: box-shadow 0.2s;">
                    {{-- Progression --}}
                    <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid #f3f4f6;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: #6b7280; margin-bottom: 0.25rem;">
                            <span>Progression pour ce frais</span>
                            <span>{{ number_format($pourcentageFrais, 1) }}% ({{ number_format($totalPayePourCeFrais, 0, ',', ' ') }}/{{ number_format($paiement->fee->montant_total ?? 0, 0, ',', ' ') }} FCFA)</span>
                        </div>
                        <div style="width: 100%; background-color: #e5e7eb; height: 6px; border-radius: 9999px;">
                            <div style="height: 6px; border-radius: 9999px; background-color: #22c55e; width: {{ min($pourcentageFrais, 100) }}%; transition: width 0.3s;"></div>
                        </div>
                    </div>

                    {{-- Paiement Info --}}
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-top: 1rem;">
                        <div style="background-color: #bbf7d0; padding: 0.5rem; border-radius: 0.5rem;">
                            💵
                        </div>

                        <div style="flex-grow: 1;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <span style="font-weight: 600; color: #111827;">{{ $paiement->fee->nom ?? 'Frais supprimé' }}</span>
                                <span style="background-color: #dcfce7; color: #166534; padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 500;">
                                    {{ number_format($paiement->montant, 0, ',', ' ') }} FCFA
                                </span>
                            </div>

                            <div style="margin-top: 0.25rem; font-size: 0.875rem; color: #6b7280; display: flex; flex-wrap: wrap; gap: 1rem;">
                                <span>📅 {{ $paiement->date->format('d/m/Y') }}</span>
                                @if($paiement->admin)
                                    <span>👤 {{ $paiement->admin->name }}</span>
                                @endif
                                <span>⏰ {{ $paiement->created_at->format('H:i') }}</span>
                            </div>
                        </div>

                        {{-- Bulle de couleur selon ancienneté --}}
                        <div style="width: 12px; height: 12px; border-radius: 9999px; 
                            background-color: {{ $couleurAge === 'green' ? '#4ade80' : ($couleurAge === 'orange' ? '#facc15' : '#9ca3af') }};" 
                            title="Il y a {{ $joursDepuis }} jour(s)">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="font-size: 0.875rem; color: #6b7280;">Aucun paiement enregistré pour cet étudiant.</p>
    @endif
</div>
