<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

    // ConsulterHistorique()
public function historique(Request $request)
{
    $signalements = $request->user()->signalements()->get()->map(fn ($s) => [
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