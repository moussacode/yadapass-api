<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PersonnelSecurite extends Model
{
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'poste',
        'phone',
    ];

    protected $hidden = ['password'];

    // Ajoute si tu veux utiliser l'auth (Authenticatable)
}
