<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;
use App\Models\AcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Fee extends Model
{

    use HasFactory;
    protected $fillable = ['nom', 'montant_total', 'class_room_id', 'academic_session_id'];
    /// Relations possibles
    
    // Une fee appartient à une classe
    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    // Une fee appartient à une session académique
    public function academicSession()
    {
        return $this->belongsTo(AcademicSession::class);
    }
}
