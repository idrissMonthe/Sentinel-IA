<?php

namespace App\Http\Controllers;

use App\Models\Alerte;
use App\Http\Requests\StoreAlerteRequest;
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
    public function store(StoreAlerteRequest $request): RedirectResponse
    {
        $data = $request->validated();

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
