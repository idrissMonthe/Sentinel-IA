<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUtilisateurRequest;
use App\Http\Requests\Admin\UpdateRoleUtilisateurRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UtilisateurController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $utilisateurs = User::latest()->paginate(20);

        return view('admin.utilisateurs.index', compact('utilisateurs'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', User::class);

        return view('admin.utilisateurs.create');
    }

    public function store(StoreUtilisateurRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'telephone' => $data['telephone'] ?? null,
            'password' => $data['password'], // haché automatiquement (cast 'hashed' du modèle)
            'role' => $data['role'],
            'statut' => 'actif',
        ]);

        return redirect()->route('admin.utilisateurs.index')->with('status', 'Utilisateur créé.');
    }

    public function updateRole(UpdateRoleUtilisateurRequest $request, User $user): RedirectResponse
    {
        $user->update(['role' => $request->validated('role')]);

        return back()->with('status', 'Rôle mis à jour.');
    }

    public function bloquer(Request $request, User $user): RedirectResponse
    {
        $this->authorize('bloquer', $user);

        $user->update(['statut' => 'bloque']);

        return back()->with('status', 'Utilisateur bloqué.');
    }

    public function debloquer(Request $request, User $user): RedirectResponse
    {
        $this->authorize('debloquer', $user);

        $user->update(['statut' => 'actif', 'tentatives_echouees' => 0]);

        return back()->with('status', 'Utilisateur débloqué.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete(); // suppression douce : les signalements déjà liés restent intacts

        return redirect()->route('admin.utilisateurs.index')->with('status', 'Utilisateur supprimé.');
    }
}