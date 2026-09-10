<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\http\Requests\Auth\RegisterUserRequest;
use App\http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Correspond à l'alternative 2.1 : création de compte (Visiteur -> Utilisateur)
    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password' => $data['password'], // haché automatiquement (cast 'hashed' sur le modèle)
            'role' => UserRole::UTILISATEUR,
        ]);

        return redirect()->route('login')->with('status', 'Compte créé avec succès. Connectez-vous pour continuer.');
    }

    // Scénario nominal + exceptions 4.1, 5.1, 5.2 de la fiche "S'authentifier"
    public function login(LoginRequest $request): RedirectResponse
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

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        // Réponse volontairement générique : elle ne révèle pas si l'adresse existe.
        Password::sendResetLink($request->only('email'));

        return back()->with('status', 'Si cette adresse est associée à un compte, un lien de réinitialisation vient d’être envoyé.');
    }

    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token, 'email' => request('email')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                    'tentatives_echouees' => 0,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.');
        }

        return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }
}
