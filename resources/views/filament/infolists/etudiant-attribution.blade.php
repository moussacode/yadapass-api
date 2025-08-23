{{-- resources/views/filament/infolists/etudiant-attribution.blade.php --}}
@php
    $attribution = App\Models\Attribution::where('etudiant_id', $getRecord()->id)
        ->with(['classRoom', 'academicSession'])
        ->latest()
        ->first();
@endphp

<div style="display: flex; flex-direction: column; gap: 1rem;">
    @if($attribution)
        <div style="background: linear-gradient(to right, #eff6ff, #eef2ff); border: 1px solid #bfdbfe; border-radius: 0.5rem; padding: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.75rem;">
                    <div style="background-color: #3b82f6; padding: 0.5rem; border-radius: 0.5rem;">
                        <x-heroicon-o-academic-cap style="width: 1.5rem; height: 1.5rem; color: white;" />
                    </div>
                    <div>
                        <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">
                            {{ $attribution->classRoom->name ?? 'Classe inconnue' }}
                        </h3>
                        <p style="font-size: 0.875rem; color: #6b7280;">
                            {{ $attribution->academicSession->name ?? 'Année scolaire inconnue' }}
                        </p>
                    </div>
                </div>
                <span style="display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; font-size: 0.875rem; font-weight: 500; background-color: #d1fae5; color: #065f46; border-radius: 9999px;">
                    Actif
                </span>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; font-size: 0.875rem;">
                <div style="background-color: white; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                    <div style="color: #6b7280; font-weight: 500;">Date d'attribution</div>
                    <div style="color: #111827; font-weight: 600;">{{ $attribution->created_at->format('d/m/Y') }}</div>
                </div>

                @if($attribution->classRoom)
                    <div style="background-color: white; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <div style="color: #6b7280; font-weight: 500;">Niveau</div>
                        <div style="color: #111827; font-weight: 600;">{{ $attribution->classRoom->niveau ?? 'N/A' }}</div>
                    </div>

                    <div style="background-color: white; padding: 0.75rem; border-radius: 0.5rem; border: 1px solid #e5e7eb;">
                        <div style="color: #6b7280; font-weight: 500;">Capacité</div>
                        <div style="color: #111827; font-weight: 600;">{{ $attribution->classRoom->capacite ?? 'N/A' }} élèves</div>
                    </div>
                @endif
            </div>

            <div style="margin-top: 1rem;">
                <a href="/admin/attributions?tableFilters[etudiant_id][value]={{ $getRecord()->id }}" 
                   style="display: inline-flex; align-items: center; padding: 0.5rem 0.75rem; font-size: 0.875rem; font-weight: 500; color: #1d4ed8; background-color: white; border: 1px solid #bfdbfe; border-radius: 0.375rem; text-decoration: none;">
                    <x-heroicon-o-eye style="width: 1rem; height: 1rem; margin-right: 0.375rem;" />
                    Voir toutes les attributions
                </a>
            </div>
        </div>
    @else
        <div style="background-color: #fef9c3; border: 1px solid #fde68a; border-radius: 0.5rem; padding: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="background-color: #facc15; padding: 0.5rem; border-radius: 0.5rem;">
                    <x-heroicon-o-exclamation-triangle style="width: 1.5rem; height: 1.5rem; color: white;" />
                </div>
                <div>
                    <h3 style="font-size: 1.125rem; font-weight: 600; color: #111827;">Aucune attribution</h3>
                    <p style="font-size: 0.875rem; color: #6b7280;">Cet étudiant n'est actuellement attribué à aucune classe.</p>
                </div>
            </div>

            <div style="margin-top: 1rem;">
                <a href="/admin/attributions/create" 
                   style="display: inline-flex; align-items: center; padding: 0.5rem 1rem; font-size: 0.875rem; font-weight: 500; color: white; background-color: #2563eb; border: none; border-radius: 0.375rem; text-decoration: none;">
                    <x-heroicon-o-plus style="width: 1rem; height: 1rem; margin-right: 0.375rem;" />
                    Attribuer à une classe
                </a>
            </div>
        </div>
    @endif
</div>
