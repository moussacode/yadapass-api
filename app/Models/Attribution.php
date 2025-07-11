<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribution extends Model
{
    //

    protected $fillable = [
        'etudiant_id',
        'class_room_id',
        'academic_session_id',
    ];
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }
    public function academicSession()
{
    return $this->belongsTo(AcademicSession::class);
}


public function carteEtudiante()
{
    return $this->hasOne(CarteEtudiante::class); // si tu fais la carte après attribution
}


}
