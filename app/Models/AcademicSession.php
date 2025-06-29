<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcademicSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'admin_id',
        'active',
    ];

    /**
     * L'administrateur qui a créé cette session.
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Les classes associées à cette session.
     */
    public function classRooms()
    {
        return $this->hasMany(ClassRoom::class);
    }
}
