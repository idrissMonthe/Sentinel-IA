<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->estAdministrateur();
    }

    public function create(User $user): bool
    {
        return $user->estAdministrateur();
    }

    // Un administrateur ne peut pas modifier son propre rôle — évite qu'il se
    // rétrograde par erreur et se retrouve bloqué hors de l'administration.
    public function updateRole(User $user, User $cible): bool
    {
        return $user->estAdministrateur() && $user->id !== $cible->id;
    }

    public function bloquer(User $user, User $cible): bool
    {
        return $user->estAdministrateur() && $user->id !== $cible->id;
    }

    public function debloquer(User $user, User $cible): bool
    {
        return $user->estAdministrateur();
    }

    // Même règle que pour le rôle : impossible de se supprimer soi-même.
    public function delete(User $user, User $cible): bool
    {
        return $user->estAdministrateur() && $user->id !== $cible->id;
    }
}