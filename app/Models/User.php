<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'statut',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }
    public function signalements(): HasMany
    {
        return $this->hasMany(Signalement::class);
    }
     public function alertesPubliees(): HasMany
    {
        return $this->hasMany(Alerte::class, 'moderateur_id');
    }
      public function estModerateur(): bool
    {
        return $this->role === UserRole::MODERATEUR;
    }

    public function estAdministrateur(): bool
    {
        return $this->role === UserRole::ADMINISTRATEUR;
    }
}
