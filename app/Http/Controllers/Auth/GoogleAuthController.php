<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\BienvenueMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->withErrors([
                'email' => 'La connexion avec Google a échoué. Réessayez ou utilisez votre email/mot de passe.',
            ]);
        }

        // Compte déjà lié à ce Google, on le retrouve directement
        $user = User::where('google_id', $googleUser->getId())->first();
        $nouveauCompte = false;

        // Sinon, un compte existe peut-être déjà avec cet email (créé via inscription
        // classique) : on lie les deux plutôt que de créer un doublon.
        if (! $user) {
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                $user->update(['google_id' => $googleUser->getId()]);
            }
        }

        // Sinon, création d'un tout nouveau compte, rôle par défaut comme pour
        // l'inscription classique (alternative 2.1 de la fiche S'authentifier).
        if (! $user) {
            $nomComplet = $googleUser->getName() ?? $googleUser->getEmail();
            [$prenom, $nom] = $this->separerNomPrenom($nomComplet);

            $user = User::create([
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $googleUser->getEmail(),
                'password' => null, // aucun mot de passe local : authentification exclusivement via Google
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'role' => UserRole::UTILISATEUR,
            ]);

            $nouveauCompte = true;
        }

        // Même vérification que pour la connexion classique (scénario d'exception 5.2)
        if ($user->statut === 'bloque') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte est temporairement bloqué. Contactez un administrateur.',
            ])->redirectTo(route('login'));
        }

        // Envoyé seulement à la création — jamais à chaque reconnexion.
        // Un échec d'envoi ne doit jamais empêcher la connexion elle-même.
        if ($nouveauCompte) {
            try {
                Mail::to($user->email)->send(new BienvenueMail($user));
            } catch (\Throwable $e) {
                Log::warning('Échec envoi email de bienvenue (inscription Google).', ['erreur' => $e->getMessage()]);
            }
        }

        Auth::login($user);
        request()->session()->regenerate();
        $user->update(['tentatives_echouees' => 0]);

        return redirect()->intended(route('accueil'));
    }

    private function separerNomPrenom(string $nomComplet): array
    {
        $parties = explode(' ', trim($nomComplet), 2);

        return [$parties[0], $parties[1] ?? ''];
    }
}