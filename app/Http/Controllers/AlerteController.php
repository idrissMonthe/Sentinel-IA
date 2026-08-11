<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AlerteController extends Controller
{
    // Consulter les alertes : public, sans authentification
    public function index()
    {
        $alertes = Alerte::where('est_publiee', true)->latest()->paginate(15);

        return view('alertes.index', compact('alertes'));
    }

    public function create(Request $request)
    {
        abort_unless($request->user()->estModerateur(), 403);

        return view('alertes.create');
    }

    // Publier une alerte — scénario nominal + alternative 5.1
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->estModerateur(), 403);

        $data = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'contenu' => ['required', 'string'],
            'action' => ['required', 'in:publier,brouillon'],
        ]);

        $alerte = $request->user()->alertesPubliees()->create([
            'titre' => $data['titre'],
            'contenu' => $data['contenu'],
            'est_publiee' => $data['action'] === 'publier',
        ]);

        return redirect()->route('alertes.index')->with(
            'status',
            $data['action'] === 'publier' ? 'Alerte publiée.' : 'Alerte enregistrée en brouillon.'
        );
    }
}