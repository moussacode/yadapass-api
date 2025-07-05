<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
