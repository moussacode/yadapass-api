<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Scan extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_heure',
        'statut_acces',
        'validation',
        'commentaire',
        'carte_etudiante_id',
        'admin_id',
    ];

    public function carteEtudiante()
    {
        return $this->belongsTo(CarteEtudiante::class);
    }

public function getMatriculeAttribute()
{
    return $this->carteEtudiante?->etudiant?->matricule ?? 'Inconnu';
}

     // Relation vers l'agent (personnel de sécurité) qui a validé ou fait le scan
    public function agent()
    {
        return $this->belongsTo(PersonnelSecurite::class, 'agent_id');
    }
}
