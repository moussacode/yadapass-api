<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteEtudiante extends Model
{
      protected $fillable = [
        'etudiant_id',
        'qr_code',
        'statut',
        'date_emission',
        'date_expiration',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
}
