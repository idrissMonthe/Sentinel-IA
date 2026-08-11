<?php

namespace App\Policies;

use App\Models\Signalement;
use App\Models\User;

class PreuvePolicy
{
    // Fournir une preuve : uniquement sur SON PROPRE signalement.
    // Le 2e paramètre est le Signalement ciblé, passé explicitement à l'appel
    // (Laravel ne peut pas le déduire automatiquement puisque Preuve n'existe pas encore).
    public function create(User $user, Signalement $signalement): bool
    {
        return $signalement->user_id === $user->id;
    }
}