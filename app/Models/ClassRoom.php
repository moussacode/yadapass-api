<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'sort',
        'academic_session_id',
        'admin_id',
    ];
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }

    public function cours()
    {
        return $this->hasMany(Cours::class, 'classe_id');
    }

    public function etudiants()
    {
        return $this->hasMany(Etudiant::class);
    }

    public function emploiDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class);
    }
    
}
