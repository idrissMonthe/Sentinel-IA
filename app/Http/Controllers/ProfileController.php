<?php

namespace App\Http\Controllers;

use App\Mail\CompteSupprimeMail;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProfileController extends Controller
{
    // ModifierProfil()
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'prenom' => ['required', 'string', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:20'],
        ]);

        $request->user()->update($data);

        return back()->with('status', 'Profil mis à jour.');
    }

    // ModifierMotDePasse()
    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Mot de passe mis à jour.');
    }

    /** Supprime le compte après vérification du mot de passe. */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'Le mot de passe saisi est incorrect.',
        ]);

        $user = $request->user();
        $email = $user->email;
        $prenom = $user->prenom;

        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        try {
            Mail::to($email)->send(new CompteSupprimeMail($prenom, $email));
        } catch (\Throwable $e) {
            Log::warning('Échec envoi e-mail de confirmation de suppression de compte.', [
                'user_id' => $user->id,
                'erreur' => $e->getMessage(),
            ]);
        }

        return redirect()->route('accueil')->with(
            'status',
            'Votre compte a été supprimé. Un e-mail de confirmation vient de vous être envoyé.'
        );
    }

    // ConsulterHistorique()
public function historique(Request $request)
{
    $statut = $request->input('statut');
    $signalements = $request->user()->signalements()
        ->when($statut, fn ($query) => $query->where('statut', $statut))
        ->with('entiteSuspecte')
        ->get()->map(fn ($s) => [
        'type' => 'signalement',
        'date' => $s->created_at,
        'label' => 'Signalement : '.($s->entiteSuspecte?->valeur ?? '—'),
        'statut' => $s->statut->label(),
        'lien' => route('signalements.show', $s),
    ]);

    $analyses = $request->user()->analyses()->get()->map(fn ($a) => [
        'type' => 'analyse',
        'date' => $a->created_at,
        'label' => $a->score_fiabilite !== null
            ? "Analyse IA (score {$a->score_fiabilite}%)"
            : 'Reformulation assistée par IA',
        'statut' => null,
        'lien' => route('analyses.show', $a),
    ]);

    $activites = $signalements->concat($analyses)->sortByDesc('date')->values();

    return view('profile.historique', compact('activites'));
}
}
