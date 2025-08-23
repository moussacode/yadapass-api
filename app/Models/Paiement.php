<?php

namespace App\Models;

use App\Models\Etudiant;
use App\Models\Fee;
use App\Models\User;
use App\Models\AcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Paiement extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'montant', 'date', 'etudiant_id', 'fee_id', 'admin_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getMontantFormateAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Calculer les arriérés mensuels pour un étudiant
     */
  public static function getArrieresMensuelsParAttribution($etudiantId, $feeId, $dateDebutAttribution)
{
    $fee = Fee::find($feeId);
    if (!$fee) {
        return ['error' => 'Frais introuvables'];
    }

    // La durée fixe de l'année scolaire = 9 mois
    $dureeAnnee = 9;

    $debut = Carbon::parse($dateDebutAttribution)->startOfMonth();
    $fin = (clone $debut)->addMonths($dureeAnnee - 1)->endOfMonth(); // 9 mois après

    $maintenant = Carbon::now()->startOfMonth();

    // Nombre total de mois = 9
    $totalMois = $dureeAnnee;

    // Calcul mensualité
    $mensualite = round($fee->montant_total / $totalMois, 2);

    // Nombre de mois écoulés depuis le début (minimum 0, max 9)
    $moisEcoules = $debut->diffInMonths($maintenant) + 1;
    if ($moisEcoules > $totalMois) $moisEcoules = $totalMois;
    if ($moisEcoules < 0) $moisEcoules = 0;

    // Ce que l'étudiant doit avoir payé jusqu'à maintenant
    $doitPayer = $moisEcoules * $mensualite;

    // Total payé
    $totalPaye = self::where('etudiant_id', $etudiantId)
        ->where('fee_id', $feeId)
        ->whereBetween('date', [$debut, $fin])
        ->sum('montant');

    // Calcul arriérés
    $arriere = max($doitPayer - $totalPaye, 0);

    // Calcul reste total
    $resteTotal = max($fee->montant_total - $totalPaye, 0);

    // Pourcentage payé
   $pourcentagePaye = $fee->montant_total > 0 ? ($totalPaye / $fee->montant_total) * 100 : 0;


    // Statut
    $statut = self::determinerStatutPaiement($totalPaye, $doitPayer, $fee->montant_total);

    return [
        'session' => [
            'debut_attribution' => $debut->format('Y-m-d'),
            'fin_attribution' => $fin->format('Y-m-d'),
        ],
        'fee' => [
            'nom' => $fee->nom ?? 'Frais scolaires',
            'montant_total' => $fee->montant_total,
        ],
        'calculs' => [
            'total_mois' => $totalMois,
            'mois_ecoules' => $moisEcoules,
            'mensualite' => $mensualite,
            'doit_payer_actuellement' => $doitPayer,
            'total_paye' => $totalPaye,
            'arriere' => $arriere,
            'reste_total' => $resteTotal,
            'pourcentage_paye' => round($pourcentagePaye, 2),
        ],
        'statut' => $statut,
        'alerte' => $arriere > 0 ? "Arriérés de {$moisEcoules} mois" : null,
    ];
}


    /**
     * Déterminer le statut de paiement
     */
    private static function determinerStatutPaiement($totalPaye, $doitPayer, $montantTotal)
    {
        if ($totalPaye >= $montantTotal) {
            return [
                'code' => 'complet',
                'libelle' => 'Entièrement payé',
                'couleur' => 'success',
                'icone' => '✅'
            ];
        }
        
        if ($totalPaye >= $doitPayer) {
            return [
                'code' => 'a_jour',
                'libelle' => 'À jour',
                'couleur' => 'success',
                'icone' => '✅'
            ];
        }
        
        if ($totalPaye > 0) {
            return [
                'code' => 'retard',
                'libelle' => 'En retard de paiement',
                'couleur' => 'warning',
                'icone' => '⚠️'
            ];
        }
        
        return [
            'code' => 'impaye',
            'libelle' => 'Non payé',
            'couleur' => 'danger',
            'icone' => '❌'
        ];
    }

    /**
     * Obtenir un résumé détaillé des paiements par mois
     */
    public static function getDetailsPaiementsMensuels($etudiantId, $feeId, $sessionId = null)
    {
        $arrieres = self::getArrieresMensuels($etudiantId, $feeId, $sessionId);
        
        if (isset($arrieres['error'])) {
            return $arrieres;
        }

        $session = AcademicSession::find($sessionId) ?? AcademicSession::where('active', true)->first();
        $debut = Carbon::parse($session->start_date)->startOfMonth();
        $mensualite = $arrieres['calculs']['mensualite'];
        
        $paiements = self::where('etudiant_id', $etudiantId)
            ->where('fee_id', $feeId)
            ->orderBy('date')
            ->get();

        $detailsMois = [];
        $cumulePayé = 0;

        for ($i = 0; $i < $arrieres['calculs']['total_mois']; $i++) {
            $moisCourant = $debut->copy()->addMonths($i);
            $estEcoule = $moisCourant->lte(Carbon::now()->startOfMonth());
            
            // Paiements pour ce mois (approximatif)
            $paiementsMois = $paiements->filter(function($p) use ($moisCourant) {
                return Carbon::parse($p->date)->isSameMonth($moisCourant);
            })->sum('montant');
            
            $cumulePayé += $paiementsMois;
            $doitAvoir = ($i + 1) * $mensualite;
            $situation = $cumulePayé >= $doitAvoir ? 'ok' : ($estEcoule ? 'retard' : 'futur');

            $detailsMois[] = [
                'mois' => $moisCourant->format('Y-m'),
                'libelle' => $moisCourant->locale('fr')->isoFormat('MMMM YYYY'),
                'mensualite' => $mensualite,
                'paiements_mois' => $paiementsMois,
                'cumule_paye' => $cumulePayé,
                'doit_avoir' => $doitAvoir,
                'ecart' => $cumulePayé - $doitAvoir,
                'situation' => $situation,
                'est_ecoule' => $estEcoule,
            ];
        }

        return [
            'resume' => $arrieres,
            'details_mensuels' => $detailsMois,
        ];
    }

    /**
     * Méthode existante améliorée
     */
    public static function getSituationFrais($etudiantId, $feeId)
    {
        $fee = Fee::find($feeId);
        if (!$fee) return null;

        $totalPaye = self::where('etudiant_id', $etudiantId)
            ->where('fee_id', $feeId)
            ->sum('montant');

        $reste = $fee->montant_total - $totalPaye;
        $pourcentage = ($totalPaye / max($fee->montant_total, 1)) * 100;

        $status = match (true) {
            $reste <= 0 => 'Soldé',
            $totalPaye > 0 => 'Partiellement payé',
            default => 'Non payé'
        };

        $statusColor = match (true) {
            $reste <= 0 => 'success',
            $totalPaye > 0 => 'warning',
            default => 'danger'
        };

        return [
            'fee' => $fee,
            'total' => $fee->montant_total,
            'paye' => $totalPaye,
            'reste' => $reste,
            'pourcentage' => round($pourcentage, 2),
            'status' => $status,
            'statusColor' => $statusColor,
            'derniers_paiements' => self::where('etudiant_id', $etudiantId)
                ->where('fee_id', $feeId)
                ->orderBy('date', 'desc')
                ->limit(5)
                ->get(),
        ];
    }
}