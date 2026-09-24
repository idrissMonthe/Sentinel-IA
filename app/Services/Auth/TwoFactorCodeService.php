<?php

namespace App\Services\Auth;

use App\Mail\CodeVerificationMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class TwoFactorCodeService
{
    private const DUREE_VALIDITE_MINUTES = 10;
    private const TENTATIVES_MAX = 5;

    public function genererEtEnvoyer(User $user): void
    {
        $code = (string) random_int(100000, 999999);

        $user->update([
            'code_2fa' => $code,
            'code_2fa_expire_a' => now()->addMinutes(self::DUREE_VALIDITE_MINUTES),
            'code_2fa_tentatives' => 0,
        ]);

        Mail::to($user->email)->send(new CodeVerificationMail($user, $code));
    }

    public function verifier(User $user, string $code): bool
    {
        if (! $user->code_2fa || ! $user->code_2fa_expire_a || $user->code_2fa_expire_a->isPast()) {
            return false;
        }

        if ($user->code_2fa_tentatives >= self::TENTATIVES_MAX) {
            return false;
        }

        if (! hash_equals($user->code_2fa, $code)) {
            $user->increment('code_2fa_tentatives');

            return false;
        }

        $user->update([
            'code_2fa' => null,
            'code_2fa_expire_a' => null,
            'code_2fa_tentatives' => 0,
            // Réutilise une colonne qui existait déjà sans jamais servir jusqu'ici :
            // le code confirme au passage que l'utilisateur possède bien cette adresse.
            'email_verified_at' => $user->email_verified_at ?? now(),
        ]);

        return true;
    }
}