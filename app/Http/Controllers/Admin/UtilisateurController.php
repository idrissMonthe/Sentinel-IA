<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    // gererUtilisateur()
    public function index(Request $request)
    {
        abort_unless($request->user()->estAdministrateur(), 403);

        $utilisateurs = User::latest()->paginate(20);

        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

    public function bloquer(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->estAdministrateur(), 403);

        $user->update(['statut' => 'bloque']);

        return back()->with('status', 'Utilisateur bloqué.');
    }

    public function debloquer(Request $request, User $user): RedirectResponse
    {
        abort_unless($request->user()->estAdministrateur(), 403);

        $user->update(['statut' => 'actif', 'tentatives_echouees' => 0]);

        return back()->with('status', 'Utilisateur débloqué.');
    }
}