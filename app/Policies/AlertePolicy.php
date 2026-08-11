<?php

namespace App\Policies;

use App\Models\User;

class AlertePolicy
{
    // Publier une alerte : réservé au Modérateur (cf. correction de la relation "Publie")
    public function create(User $user): bool
    {
        return $user->estModerateur();
    }
}