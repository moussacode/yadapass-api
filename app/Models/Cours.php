<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cours extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'code',
        'enseignant',
        'admin_id',
    ];

    // Relation : un cours appartient à plusieur classe
 
    public function emploisDuTemps()
{
    return $this->hasMany(EmploiDuTemps::class);
}
public function classRooms()
{
    return $this->belongsToMany(ClassRoom::class, 'class_room_cours');
}

    // Relation : un cours appartient à un administrateur (créateur/gestionnaire)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
     public function getAcademicSessionsAttribute()
    {
        return $this->classRooms()->with('academicSession')->get()
            ->pluck('academicSession')->unique('id');
    }
    
}
