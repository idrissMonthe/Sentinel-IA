<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'statut',
        'role',
        'google_id',
        'tentatives_echouees',
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
    public function analyses(): HasMany 
    { 
        return $this->hasMany(Analyse::class); 
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
