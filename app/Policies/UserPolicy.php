<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    // gererUtilisateur() : liste des utilisateurs, réservé Administrateur
    public function viewAny(User $user): bool
    {
        return $user->estAdministrateur();
    }

    public function bloquer(User $user, User $cible): bool
    {
        // un administrateur ne peut pas se bloquer lui-même
        return $user->estAdministrateur() && $user->id !== $cible->id;
    }

    public function debloquer(User $user, User $cible): bool
    {
        return $user->estAdministrateur();
    }
}