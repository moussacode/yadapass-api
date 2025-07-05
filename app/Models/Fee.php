<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClassRoom;
use App\Models\AcademicSession;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Fee extends Model
{

    use HasFactory;
    protected $fillable = ['nom', 'type', 'montant_total', 'class_room_id'];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
    

    
}
