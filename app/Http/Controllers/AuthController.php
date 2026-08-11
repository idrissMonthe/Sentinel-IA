<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\http\Requests\Auth\RegisterUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Correspond à l'alternative 2.1 : création de compte (Visiteur -> Utilisateur)
    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'telephone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password' => $data['password'], // haché automatiquement (cast 'hashed' sur le modèle)
            'role' => UserRole::UTILISATEUR,
        ]);

        Auth::login($user);

        return redirect()->route('accueil');
    }

    // Scénario nominal + exceptions 4.1, 5.1, 5.2 de la fiche "S'authentifier"
    public function login(RegisterUserRequest $request): RedirectResponse
    {
        // 4.1 : champs manquants -> géré automatiquement par validate()
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        // 5.2 : compte bloqué -> on vérifie AVANT de tenter l'authentification
        if ($user && $user->statut === 'bloque') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte est temporairement bloqué. Contactez un administrateur.',
            ]);
        }

        if (! Auth::attempt($credentials)) {
            // 5.1 : identifiants incorrects -> incrémenter le compteur, bloquer au seuil
            if ($user) {
                $user->increment('tentatives_echouees');
                if ($user->tentatives_echouees >= 5) {
                    $user->update(['statut' => 'bloque']);
                }
            }

            // Message générique volontairement imprécis (bonne pratique de sécurité,
            // cf. remarque de la fiche : ne pas préciser lequel des deux champs est fautif)
            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ]);
        }

        // Connexion réussie : réinitialiser le compteur
        $request->session()->regenerate();
        Auth::user()->update(['tentatives_echouees' => 0]);

        return redirect()->intended(route('accueil'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }
}