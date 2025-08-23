<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Models\Etudiant;
use App\Models\Fee;
use App\Models\AcademicSession;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaiementController extends Controller
{
    /**
     * Obtenir la situation des arriérés d'un étudiant
     */
    public function getArrieres(Request $request, $etudiantId): JsonResponse
    {
        try {
            $feeId = $request->input('fee_id');
            $sessionId = $request->input('session_id');

            if (!$feeId) {
                // Prendre les frais de scolarité par défaut
                $fee = Fee::where('type', 'scolarite')->first();
                $feeId = $fee?->id;
            }

            if (!$feeId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucun frais trouvé'
                ], 404);
            }

            $arrieres = Paiement::getArrieresMensuels($etudiantId, $feeId, $sessionId);

            if (isset($arrieres['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $arrieres['error'],
                    'details' => $arrieres
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $arrieres
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul des arriérés',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir le détail mensuel des paiements
     */
    public function getDetailsMensuels(Request $request, $etudiantId): JsonResponse
    {
        try {
            $feeId = $request->input('fee_id');
            $sessionId = $request->input('session_id');

            $details = Paiement::getDetailsPaiementsMensuels($etudiantId, $feeId, $sessionId);

            if (isset($details['error'])) {
                return response()->json([
                    'success' => false,
                    'message' => $details['error']
                ], 400);
            }

            return response()->json([
                'success' => true,
                'data' => $details
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Intégrer les arriérés dans les infos de l'étudiant (pour votre API existante)
     */public function getInfosAvecArrieres(string $matricule): JsonResponse
{
    try {
        $matricule = strtoupper(trim($matricule));
        $etudiant = Etudiant::with('attributions')->where('matricule', $matricule)->first();

        if (!$etudiant) {
            return response()->json([
                'success' => false,
                'message' => 'Étudiant non trouvé'
            ], 404);
        }

        $fee = Fee::where('type', 'scolarite')->first();
        $arrieres = null;

        if ($fee) {
            $attribution = $etudiant->attributions->sortBy('created_at')->first();
            if ($attribution) {
                $dateDebutAttribution = $attribution->created_at;
                $arrieres = Paiement::getArrieresMensuelsParAttribution($etudiant->id, $fee->id, $dateDebutAttribution);
            } else {
                // Fallback si pas d’attribution, on peut prendre la session active ou null
                $arrieres = Paiement::getArrieresMensuels($etudiant->id, $fee->id);
            }
        }

        $data = [
            'matricule' => $etudiant->matricule,
            'nom' => $etudiant->nom . ' ' . $etudiant->prenom,
            'photo' => $etudiant->photo ? url('storage/' . $etudiant->photo) : null,
            'statut' => $etudiant->statut,
            'situation_financiere' => $arrieres,
            'derniere_mise_a_jour' => now()->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la récupération des informations',
            'error' => $e->getMessage()
        ], 500);
    }
}

}