<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoomCours extends Model
{
    
    // This model is used to manage the many-to-many relationship between ClassRoom and Cours
    protected $fillable = [
        'class_room_id',
        'cours_id',
    ];
    public function admin()
{
    return $this->belongsTo(User::class, 'admin_id');
}


}
