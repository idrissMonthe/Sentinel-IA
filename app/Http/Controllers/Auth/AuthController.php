<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterUserRequest;
use App\Http\Requests\Auth\VerifierCodeRequest;
use App\Mail\BienvenueMail;
use App\Models\User;
use App\Services\Auth\TwoFactorCodeService;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password' => $data['password'],
            'role' => UserRole::UTILISATEUR,
        ]);

        try {
            Mail::to($user->email)->send(new BienvenueMail($user));
        } catch (\Throwable $e) {
            Log::warning('Échec envoi email de bienvenue (inscription classique).', ['erreur' => $e->getMessage()]);
        }

        return redirect()
            ->route('login')
            ->with(
                'status',
                'Compte créé avec succès. Connectez-vous pour continuer.'
            );
    }

    public function login(
        LoginRequest $request,
        TwoFactorCodeService $service
    ): RedirectResponse {
        $credentials = $request->validated();

        $user = User::where('email', $credentials['email'])->first();

        if ($user && $user->statut === 'bloque') {
            throw ValidationException::withMessages([
                'email' => 'Votre compte est temporairement bloqué. Contactez un administrateur.',
            ])->redirectTo(route('login'));
        }

        if (! $user || ! Auth::validate($credentials)) {
            if ($user) {
                $user->increment('tentatives_echouees');

                if ($user->tentatives_echouees >= 5) {
                    $user->update([
                        'statut' => 'bloque',
                    ]);
                }
            }

            throw ValidationException::withMessages([
                'email' => 'Identifiants incorrects.',
            ])->redirectTo(route('login'));
        }

        $request->session()->put('2fa_user_id', $user->id);

        $service->genererEtEnvoyer($user);

        return redirect()->route('verification.code');
    }

    public function afficherFormulaireCode(Request $request)
    {
        if (! $request->session()->has('2fa_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verification-code');
    }

    public function verifierCode(
        VerifierCodeRequest $request,
        TwoFactorCodeService $service
    ): RedirectResponse {
        $userId = $request->session()->get('2fa_user_id');

        if (! $userId) {
            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Session expirée, reconnectez-vous.',
                ]);
        }

        $user = User::find($userId);

        if (
            ! $user ||
            ! $service->verifier($user, $request->validated('code'))
        ) {
            throw ValidationException::withMessages([
                'code' => 'Code invalide ou expiré.',
            ])->redirectTo(route('verification.code'));
        }

        Auth::login($user);

        $request->session()->forget('2fa_user_id');
        $request->session()->regenerate();

        $user->update([
            'tentatives_echouees' => 0,
        ]);

        return redirect()->intended(route('accueil'));
    }

    public function renvoyerCode(
        Request $request,
        TwoFactorCodeService $service
    ): RedirectResponse {
        $userId = $request->session()->get('2fa_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if ($user) {
            $service->genererEtEnvoyer($user);
        }

        return back()->with(
            'status',
            'Un nouveau code vous a été envoyé.'
        );
    }

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => [
                'required',
                'email:rfc',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/i',
            ],
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return back()->with(
            'status',
            'Si cette adresse est associée à un compte, un lien de réinitialisation vient d’être envoyé.'
        );
    }

    public function showResetPasswordForm(string $token)
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => request('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => [
                'required',
                'email:rfc',
                'regex:/^[^@\s]+@[^@\s]+\.[^@\s]+$/i',
            ],
            'password' => [
                'required',
                'confirmed',
                'min:8',
            ],
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => $password,
                    'remember_token' => str()->random(60),
                    'tentatives_echouees' => 0,
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()
                ->route('login')
                ->with(
                    'status',
                    'Votre mot de passe a été réinitialisé. Vous pouvez vous connecter.'
                );
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors([
                'email' => __($status),
            ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('accueil');
    }
}