<?php

namespace App\Policies;

use App\Models\Signalement;
use App\Models\User;

class SignalementPolicy
{
    // Suivre un signalement : le déclarant, ou un modérateur/administrateur en modération
    public function view(User $user, Signalement $signalement): bool
    {
        return $signalement->user_id === $user->id
            || $user->estModerateur()
            || $user->estAdministrateur();
    }

    // Accès à la file d'attente de modération (ModerationController::index/doublons)
    // Ability de classe (pas d'instance précise) : voir usage $this->authorize('moderer', Signalement::class)
    public function moderer(User $user): bool
    {
        return $user->estModerateur();
    }

    public function valider(User $user, Signalement $signalement): bool
    {
        return $user->estModerateur();
    }

    public function rejeter(User $user, Signalement $signalement): bool
    {
        return $user->estModerateur();
    }
}