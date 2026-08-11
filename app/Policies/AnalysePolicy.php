<?php

namespace App\Policies;

use App\Models\Analyse;
use App\Models\User;

class AnalysePolicy
{
    public function view(User $user, Analyse $analyse): bool
    {
        return $analyse->user_id === $user->id;
    }
}