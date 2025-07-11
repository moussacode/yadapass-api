<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\EmploiDuTemps;

class Etudiant extends Model
{
    protected $fillable = [
        'matricule',
        'nom',
        'prenom',
        'photo',
        'adresse',
        'telephone',
        'date_naissance',
        'genre',
        'nationalite',
    ];

    public function carteEtudiante()
    {
        return $this->hasOne(CarteEtudiante::class);
    }
    public function attributions()
{
    return $this->hasMany(Attribution::class);
}
public function getNomCompletAttribute()
{
    return $this->nom . ' ' . $this->prenom;
}
public function paiements()
{
    return $this->hasMany(Paiement::class);
}


public function getEmploiDuTemps()
{
    $attribution = $this->attributions()->latest()->first();

    if (!$attribution || !$attribution->classRoom) {
        return [];
    }

    // Charger les emplois du temps de la classe, avec les relations utiles
    return EmploiDuTemps::with(['cours', 'salle'])
        ->where('class_room_id', $attribution->classRoom->id)
        ->orderByRaw("FIELD(jour, 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche')")
        ->orderBy('heure_debut')
        ->get()
        ->map(function ($e) {
            return [
                'jour' => $e->jour,
                'creneau' => $e->creneau,
                'cours' => $e->cours->nom ?? 'N/A',
                'salle' => $e->salle->nom ?? 'N/A',
            ];
        });
}

}
