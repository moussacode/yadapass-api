<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class PersonnelSecurite extends Authenticatable
{
     use HasApiTokens, HasFactory, Notifiable;
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'poste',
        'phone',
    ];

   

    // Ajoute si tu veux utiliser l'auth (Authenticatable)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Optionnel: accessor pour le nom complet
    public function getFullNameAttribute()
    {
        return $this->nom . ' ' . $this->prenom;
    }
}
