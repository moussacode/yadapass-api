<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmploiDuTemps extends Model
{
    use HasFactory;

    protected $table = 'emploi_du_temps';

    protected $fillable = [
        'jour',
        'heure_debut',
        'heure_fin',
        'cours_id',
        'class_room_id',
        'salle_id',
        'admin_id',
    ];
    protected $casts = [
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
    ];

    // Relation vers Cours
    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }

    // Relation vers ClassRoom
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // Relation vers Salle
    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }

    // Relation vers Admin (User)
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
       // Méthodes utiles
    public function getDureeAttribute() {
        return $this->heure_debut->diffInMinutes($this->heure_fin);
    }

    public function getCreneauAttribute() {
        return $this->heure_debut->format('H:i') . ' - ' . $this->heure_fin->format('H:i');
    }
}
