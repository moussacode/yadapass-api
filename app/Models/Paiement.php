<?php

namespace App\Models;

use App\Models\Etudiant;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    // App\Models\Paiement.php

public static function getSituationFrais($etudiantId, $feeId)
{
    $fee = \App\Models\Fee::find($feeId);
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
        'pourcentage' => $pourcentage,
        'status' => $status,
        'statusColor' => $statusColor,
    ];
}

}
