<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Barryvdh\DomPDF\Facade\Pdf;
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
        'date_naissance' => $etudiant->date_naissance  ?? 'N/A',
        'statut' => $this->statut,
        ];
    }
 

public function generatePdf()
{
    $carteInfos = $this->getCarteInfos();

    $pdf = Pdf::loadView('carte.print', [
        'carte' => $this,
        'carteInfos' => $carteInfos,
    ])
    ->setPaper([0, 0, 242.83, 153.00]) // Format carte
    ->setOptions([
        'dpi' => 300,
        'defaultFont' => 'Arial',
        'isHtml5ParserEnabled' => true,
        'isRemoteEnabled' => true,
        'margin-top' => 0,
        'margin-right' => 0,
        'margin-bottom' => 0,
        'margin-left' => 0,
    ]);

    return $pdf->output(); // retourne le contenu du PDF (utilisé dans le mail)
}

    public function attribution()
    {
        return $this->belongsTo(Attribution::class);
    }
}
