<?php

namespace App\Http\Controllers;

use App\Models\EntiteSuspecte;
use App\Models\Signalement;
use App\Http\Requests\StoreSignalementRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SignalementController extends Controller
{
    // Suivre un signalement : liste des signalements du connecté (redondant avec
    // ConsulterHistorique, mais correspond à un cas d'utilisation distinct dans votre diagramme)
    public function index(Request $request)
    {
        $signalements = $request->user()->signalements()->latest()->paginate(15);

        return view('signalements.index', compact('signalements'));
    }

    public function create()
    {
        return view('signalements.create');
    }

    // Scénario nominal de la fiche "Signaler une arnaque", alternatives 4.1 et 8.1
    public function store(StoreSignalementRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:numero,email,lien'],
            'valeur' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
        ]);

        // 8.1 : entité déjà existante ou création à la volée
        $entite = EntiteSuspecte::firstOrCreate(
            ['type' => $data['type'], 'valeur' => $data['valeur']],
        );

        $doublonPotentiel = $entite->nombre_signalement >= 5;

        $signalement = $request->user()->signalements()->create([
            'entite_suspecte_id' => $entite->id,
            'description' => $data['description'],
            'ville' => $data['ville'] ?? null,
            // statut par défaut = EN_ATTENTE (défini dans la migration)
        ]);

        return redirect()
            ->route('signalements.show', $signalement)
            ->with('status', $doublonPotentiel
                ? 'Signalement enregistré. Cette entité a déjà été largement signalée.'
                : 'Signalement enregistré, en attente de modération.');
    }

    public function show(Signalement $signalement)
    {
        // Seul le déclarant, un modérateur ou un administrateur peuvent voir le détail
        $user = Auth::user() ?? abort(401);
        abort_unless(
            $signalement->user_id === $user->id || $user->estModerateur() || $user->estAdministrateur(),
            403
        );

        $signalement->load(['preuves', 'entiteSuspecte', 'moderateur']);

        return view('signalements.show', compact('signalement'));
    }
}