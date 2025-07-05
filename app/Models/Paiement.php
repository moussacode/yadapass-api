<?php

namespace App\Models;

use App\Models\Etudiant;
use App\Models\Fee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{

    use HasFactory;
   protected $fillable = [
        'montant', 'date', 'etudiant_id', 'fee_id', 'admin_id'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class);
    }

    public function fee()
    {
        return $this->belongsTo(Fee::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function getMontantFormateAttribute()
    {
        return number_format($this->montant, 0, ',', ' ') . ' FCFA';
    }
}
