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
        $signalements = $request->user()
            ->signalements()
            ->with('entiteSuspecte')
            ->latest()
            ->paginate(15);

        return view('profile.historique', compact('signalements'));
    }
}