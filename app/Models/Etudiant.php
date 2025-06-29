<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
    protected $fillable = [
        'matricule',
        'user_id',
        'photo',
        'adresse',
        'telephone',
        'date_naissance',
        'genre',
        'class_room_id',
    ];

   

    /**
     * Get the carte etudiante associated with the etudiant.
     */
   public function carteEtudiante()
{
    return $this->hasOne(CarteEtudiante::class);
}
public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

}
