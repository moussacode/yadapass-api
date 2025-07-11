<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CarteEtudianteApiController extends Controller
{
    /**
     * Obtenir les informations d'un étudiant par matricule
     */
    public function getInfos(string $matricule): JsonResponse
    {
        try {
            // Validation du matricule
            if (empty($matricule)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Matricule requis.'
                ], 400);
            }

            // Normalisation du matricule
            $matricule = strtoupper(trim($matricule));

            // Recherche de l'étudiant avec eager loading pour optimiser les requêtes
            $etudiant = Etudiant::with([
                'attributions' => function($query) {
                    $query->latest()->with([
                        'classRoom' => function($query) {
                            $query->with([
                                'emploiDuTemps' => function($query) {
                                    $query->with(['cours', 'salle'])
                                          ->orderBy('jour')
                                          ->orderBy('heure_debut');
                                }
                            ]);
                        }
                    ]);
                }
            ])->where('matricule', $matricule)->first();

            if (!$etudiant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Étudiant non trouvé avec le matricule: ' . $matricule
                ], 404);
            }

            // Récupération de l'attribution la plus récente
            $attribution = $etudiant->attributions->first();

            // Préparation de l'emploi du temps
            $emplois = $this->formatEmploiDuTemps($attribution);

            // Préparation des données de réponse
            $data = [
                'matricule' => $etudiant->matricule,
                'photo' => $etudiant->photo ? url('storage/' . $etudiant->photo) : null,
                'nationalite' => $etudiant->nationalite ?? 'Non renseignée',
                'nom' => $this->formatNomComplet($etudiant->nom, $etudiant->prenom),
                'statut' => $etudiant->statut ?? 'Non défini',
                'attribution' => $this->formatAttribution($attribution),
                'emploi_du_temps' => $emplois,
                'total_cours' => count($emplois),
                'derniere_mise_a_jour' => now()->format('Y-m-d H:i:s'),
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur API getInfos', [
                'matricule' => $matricule ?? 'N/A',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des informations.',
                'error' => config('app.debug') ? $e->getMessage() : 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Formater le nom complet de l'étudiant
     */
    private function formatNomComplet(?string $nom, ?string $prenom): string
    {
        $nomComplet = trim(($nom ?? '') . ' ' . ($prenom ?? ''));
        return empty($nomComplet) ? 'Nom non renseigné' : $nomComplet;
    }

    /**
     * Formater les informations d'attribution
     */
    private function formatAttribution($attribution): ?array
    {
        if (!$attribution) {
            return null;
        }

        return [
            'id' => $attribution->id,
            'classe' => $attribution->classRoom->name ?? 'Classe non assignée',
            'date_attribution' => $attribution->created_at?->format('Y-m-d H:i:s'),
            'statut' => $attribution->carteEtudiante->statut ?? 'Actif',
        ];
    }

    /**
     * Formater l'emploi du temps
     */
    private function formatEmploiDuTemps($attribution): array
    {
        if (!$attribution || !$attribution->classRoom) {
            return [];
        }

        $emplois = $attribution->classRoom->emploiDuTemps ?? collect();

        return $emplois->map(function ($emploi) {
            return [
                'id' => $emploi->id ?? null,
                'jour' => $this->formatJour($emploi->jour),
                'creneau' => $this->formatCreneau($emploi),
                'cours' => [
                    'nom' => $emploi->cours->nom ?? 'Cours non défini',
                    'code' => $emploi->cours->code ?? null,
                    'enseignant' => $emploi->cours->enseignant ?? 'Non assigné',
                ],
                'salle' => [
                    'nom' => $emploi->salle->nom ?? 'Salle non définie',
                    'code' => $emploi->salle->code ?? null,
                    'capacite' => $emploi->salle->capacite ?? null,
                ],
                'heure_debut' => $emploi->heure_debut ?? null,
                'heure_fin' => $emploi->heure_fin ?? null,
            ];
        })->toArray();
    }

    /**
     * Formater le nom du jour
     */
    private function formatJour(?string $jour): string
    {
        if (!$jour) return 'Jour non défini';

        $jours = [
            'monday' => 'Lundi',
            'tuesday' => 'Mardi', 
            'wednesday' => 'Mercredi',
            'thursday' => 'Jeudi',
            'friday' => 'Vendredi',
            'saturday' => 'Samedi',
            'sunday' => 'Dimanche',
        ];

        return $jours[strtolower($jour)] ?? ucfirst($jour);
    }

    /**
     * Formater le créneau horaire
     */
    private function formatCreneau($emploi): string
    {
        if (!$emploi || (!$emploi->heure_debut && !$emploi->heure_fin)) {
            return 'Créneau non défini';
        }

        $debut = $emploi->heure_debut ?? 'XX:XX';
        $fin = $emploi->heure_fin ?? 'XX:XX';

        return "{$debut} - {$fin}";
    }

    /**
     * Endpoint pour obtenir seulement l'emploi du temps
     */
    public function getEmploiDuTemps(string $matricule): JsonResponse
    {
        try {
            $etudiant = Etudiant::with([
                'attributions' => function($query) {
                    $query->latest()->with([
                        'classRoom.emploiDuTemps' => function($query) {
                            $query->with(['cours', 'salle'])
                                  ->orderBy('jour')
                                  ->orderBy('heure_debut');
                        }
                    ]);
                }
            ])->where('matricule', strtoupper(trim($matricule)))->first();

            if (!$etudiant) {
                return response()->json([
                    'success' => false,
                    'message' => 'Étudiant non trouvé.'
                ], 404);
            }

            $attribution = $etudiant->attributions->first();
            $emplois = $this->formatEmploiDuTemps($attribution);

            return response()->json([
                'success' => true,
                'data' => [
                    'matricule' => $etudiant->matricule,
                    'classe' => $attribution->classRoom->nom ?? 'Non assignée',
                    'emploi_du_temps' => $emplois,
                    'total_cours' => count($emplois),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur API getEmploiDuTemps', [
                'matricule' => $matricule,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'emploi du temps.'
            ], 500);
        }
    }
}