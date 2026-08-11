<?php

namespace App\Http\Controllers;

use App\Models\EntiteSuspecte;
use App\Models\Signalement;
use App\Http\Requests\SuggestionRedactionRequest;
use App\Models\Analyse;
use App\Services\Analyse\AnalyseIAService;
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

    $entite = EntiteSuspecte::firstOrCreate(
        ['type' => $data['type'], 'valeur' => $data['valeur']],
    );

    $doublonPotentiel = $entite->nombre_signalement >= 5;

    $signalement = $request->user()->signalements()->create([
        'entite_suspecte_id' => $entite->id,
        'analyse_id' => $data['analyse_id'] ?? null, // <-- ajout : lien vers l'analyse réutilisée/générée
        'description' => $data['description'],
        'ville' => $data['ville'] ?? null,
    ]);

        return redirect()
            ->route('signalements.show', $signalement)
            ->with('status', $doublonPotentiel
                ? 'Signalement enregistré. Cette entité a déjà été largement signalée.'
                : 'Signalement enregistré, en attente de modération.');
    }

    public function show(Signalement $signalement)
    {
       $this->authorize('view', $signalement);

    $signalement->load(['preuves', 'entiteSuspecte', 'moderateur']);

        return view('signalements.show', compact('signalement'));
    }
    public function suggestionIA(SuggestionRedactionRequest $request, AnalyseIAService $service): RedirectResponse
{
    $data = $request->validated();

    if (! empty($data['analyse_id'])) {
        // Chemin gratuit : réutilisation d'une analyse déjà payée
        $analyse = Analyse::findOrFail($data['analyse_id']);
        $appelFacture = false;
    } else {
        // Chemin payant : un seul appel, uniquement parce que l'utilisateur
        // a cliqué explicitement sur "Aide à la rédaction" (pas de déclenchement automatique)
        $quota = config('vigilia.quota_analyses_jour');
        $utiliseAujourdhui = $request->user()->analyses()->whereDate('created_at', today())->count();

        if ($utiliseAujourdhui >= $quota) {
            return back()->withErrors([
                'analyse_id' => "Quota quotidien d'analyses IA atteint ({$quota}/jour). Réessayez demain, ou décrivez le signalement manuellement.",
            ]);
        }

        [$score, $conclusion] = $service->analyser($data['type'], $data['contenu']);

        $analyse = $request->user()->analyses()->create([
            'date_analyse' => now(),
            'score_fiabilite' => $score,
            'conclusion' => $conclusion,
            ]);
        $appelFacture = true;
    }

    $suggestion = sprintf(
        "Contenu suspect détecté (score de fiabilité IA : %s%%).\n\n%s",
        $analyse->score_fiabilite,
        $analyse->conclusion
    );

    // Renvoie vers le formulaire de création avec la description pré-remplie,
    // modifiable par l'utilisateur avant validation finale (jamais envoyée telle quelle)
    return back()->withInput([
        'description' => $suggestion,
        'analyse_id' => $analyse->id,
    ])->with('status', $appelFacture
        ? 'Suggestion générée (1 appel IA consommé).'
        : 'Suggestion générée à partir d\'une analyse existante — aucun appel IA supplémentaire.');
}
}