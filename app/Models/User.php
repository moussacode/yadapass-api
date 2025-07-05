<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasName;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, HasName
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'phone',
        'role',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Définition des rôles
    const ROLE_ADMIN = 'admin';
    

    // Relations vers les rôles spécifiques
    public function administrateur()
    {
        return $this->hasOne(Administrateur::class);
    }


    // Méthodes utilitaires
    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    
    // Autorisation d'accès à Filament
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin(); // Seuls les admins accèdent à Filament
    }

    // ✅ Implémentation de HasName pour Filament
    public function getFilamentName(): string
    {
        return trim("{$this->prenom} {$this->nom}") ?: 'Utilisateur';
    }

    // ✅ Accesseur pour l'attribut 'name' que Filament recherche
    public function getNameAttribute(): string
    {
        return $this->getFilamentName();
    }
}