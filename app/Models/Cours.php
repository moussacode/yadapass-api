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
        'class_room_id',
        'admin_id',
    ];

    // Relation : un cours appartient à une classe
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
    public function emploisDuTemps()
{
    return $this->hasMany(EmploiDuTemps::class);
}

    // Relation : un cours appartient à un administrateur (créateur/gestionnaire)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
