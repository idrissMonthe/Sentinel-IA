<?php

namespace App\Http\Controllers;

use App\Http\Requests\RechercherEntiteRequest;
use App\Models\EntiteSuspecte;
use Illuminate\Http\Request;

class EntiteSuspecteController extends Controller
{
    // Rechercher (numéro / email / lien) — accessible au Visiteur, PAS de middleware 'auth' ici,
    // conformément à la réorientation du diagramme de cas d'utilisation (rôle préventif).
    public function index(RechercherEntiteRequest $request)
    {
        $request->validate([
            'type' => ['nullable', 'in:numero,email,lien'],
            'valeur' => ['nullable', 'string', 'max:255'],
        ]);

        $resultats = EntiteSuspecte::query()
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->type))
            ->when($request->filled('valeur'), fn ($q) => $q->where('valeur', 'like', '%'.$request->valeur.'%'))
            ->orderByDesc('nombre_signalement')
            ->paginate(20);

        return view('base-collaborative.index', compact('resultats'));
    }

    public function show(EntiteSuspecte $entiteSuspecte)
    {
        // Uniquement les signalements validés sont exposés publiquement
        $signalements = $entiteSuspecte->signalements()
            ->where('statut', 'valide')
            ->latest()
            ->paginate(5);

        return view('base-collaborative.show', compact('entiteSuspecte', 'signalements'));
    }
}