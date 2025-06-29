<?php

namespace App\Models;

use App\Models\Etudiant;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payement extends Model
{

    use HasFactory;
    protected $fillable = [
    'montant',
    'date',
    'etudiant_id',
    'fee_id',
    'admin_id',
];
     // Relation vers l'étudiant qui effectue le paiement
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    // Relation vers le frais (fee) payé
    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    // Relation vers l'admin qui a validé/enregistré le paiement (optionnel)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
