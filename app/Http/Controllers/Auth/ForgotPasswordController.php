<?php
// app/Http/Controllers/Auth/ForgotPasswordController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function envoyer(ForgotPasswordRequest $request): RedirectResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        // Un compte Google sans mot de passe local ne peut pas en réinitialiser un.
        if ($user && is_null($user->password)) {
            return back()->withErrors([
                'email' => 'Ce compte est connecté via Google et n\'a pas de mot de passe local. Utilisez "Continuer avec Google" pour vous connecter.',
            ]);
        }

        $statut = Password::sendResetLink($request->only('email'));

        return $statut === Password::RESET_LINK_SENT
            ? back()->with('status', 'Un lien de réinitialisation vous a été envoyé par email.')
            : back()->withErrors(['email' => __($statut)]);
    }
}