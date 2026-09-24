<?php
// app/Http/Controllers/Auth/ResetPasswordController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $statut = Password::reset(
            $request->validated(),
            function (User $user, string $password) {
                $user->update([
                    'password' => $password, 
                    'tentatives_echouees' => 0,
                    'statut' => 'actif', 
                ]);
            }
        );

        return $statut === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Mot de passe réinitialisé, vous pouvez vous connecter.')
            : back()->withErrors(['email' => __($statut)]);
    }
}