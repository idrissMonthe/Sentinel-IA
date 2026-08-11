<?php

namespace App\Services\Analyse;

use App\Models\User;

class QuotaAnalyseService
{
    public function quotaAtteint(User $user): bool
    {
        $quota = config('sentinel_ia.quota_analyses_jour');
        $utiliseAujourdhui = $user->analyses()->whereDate('created_at', today())->count();

        return $utiliseAujourdhui >= $quota;
    }
}