<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSignalementRequest;
use App\Http\Requests\SuggestionRedactionRequest;
use App\Models\Analyse;
use App\Models\EntiteSuspecte;
use App\Models\Signalement;
use App\Services\Analyse\AnalyseIAService;
use App\Services\Analyse\QuotaAnalyseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

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

    // Scénario nominal de la fiche "Signaler une arnaque", alternatives 4.1 et 8.1.
    // Correction : $request->validated() remplace l'ancien $request->validate([...]) en dur,
    // qui dupliquait la validation déjà faite par StoreSignalementRequest et ignorait analyse_id.
    public function store(StoreSignalementRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // 8.1 : entité déjà existante ou création à la volée
        $entite = EntiteSuspecte::firstOrCreate(
            ['type' => $data['type'], 'valeur' => $data['valeur']],
        );

        $doublonPotentiel = $entite->nombre_signalement >= 5;

        $signalement = $request->user()->signalements()->create([
            'entite_suspecte_id' => $entite->id,
            'analyse_id' => $data['analyse_id'] ?? null, // lien vers l'analyse réutilisée/générée
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

    // Rédiger un signalement assisté par IA : <<extend>> de Signaler une arnaque,
    // <<include>> de Analyser un contenu avec l'IA.
    public function suggestionIA(
        SuggestionRedactionRequest $request,
        AnalyseIAService $service,
        QuotaAnalyseService $quotaService,
    ): RedirectResponse {
        $data = $request->validated();

        if (! empty($data['analyse_id'])) {
            // Réutilisation d'une analyse de fraude déjà faite (venant de analyses/show,
            // lien "Signaler cette arnaque") : gratuit, aucun nouvel appel IA.
            $analyse = Analyse::findOrFail($data['analyse_id']);

            $suggestion = sprintf(
                "Contenu suspect détecté (score de fiabilité IA : %s%%).\n\n%s",
                $analyse->score_fiabilite,
                $analyse->conclusion
            );

            return back()->withInput(['description' => $suggestion, 'analyse_id' => $analyse->id])
                ->with('status', 'Description pré-remplie à partir d\'une analyse existante — aucun appel IA supplémentaire.');
        }

        // Aide à la rédaction pure : reformulation, jamais une analyse de fraude (c'était le bug).
        if ($quotaService->quotaAtteint($request->user())) {
            return back()->withErrors([
                'contenu' => "Quota quotidien d'analyses IA atteint. Réessayez demain, ou décrivez le signalement manuellement.",
            ]);
        }

        $descriptionAmelioree = $service->ameliorerRedaction($data['type'], $data['contenu']);

        // Trace conservée pour le comptage du quota (toujours un appel IA facturé),
        // sans score de fiabilité : ce n'est pas une analyse de fraude.
        $analyse = $request->user()->analyses()->create([
            'date_analyse' => now(),
            'score_fiabilite' => null,
            'conclusion' => $descriptionAmelioree,
        ]);

        return back()->withInput(['description' => $descriptionAmelioree, 'analyse_id' => $analyse->id])
            ->with('status', 'Description reformulée par l\'IA (1 appel consommé) — relisez-la avant de valider.');
    }
}