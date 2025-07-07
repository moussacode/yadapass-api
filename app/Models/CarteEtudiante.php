<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarteEtudiante extends Model
{
      protected $fillable = [
        'etudiant_id',
        'attribution_id',
        'qr_data', 
        'qr_code',
        'statut',
        'date_emission',
    ];

    public function etudiant()
    {
        return $this->hasOneThrough(Etudiant::class, Attribution::class, 'id', 'id', 'attribution_id', 'etudiant_id');
    }
    // Méthodes pour récupérer les informations de la carte
    public function getCarteInfos()
    {
        $attribution = $this->attribution;
        $etudiant = $attribution->etudiant;
        
        return [
             'photo' => $etudiant->photo,
        'nom_complet' => $etudiant->nom . ' ' . $etudiant->prenom,
        'matricule' => $etudiant->matricule,
        'classe' => $attribution->classRoom->name ?? 'Non définie',
        'annee_academique' => $attribution->academicSession->name ?? 'Non définie',
        'qr_code' => $this->qr_code,      // Texte QR (matricule)
        'qr_data' => $this->qr_data,      // Image QR base64 à afficher
        'date_emission' => $this->date_emission,
        'statut' => $this->statut,
        ];
    }
    public function attribution()
    {
        return $this->belongsTo(Attribution::class);
    }
}
