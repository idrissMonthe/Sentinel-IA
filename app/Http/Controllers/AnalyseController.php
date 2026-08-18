<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Http\Requests\StoreAnalyseRequest;
use App\Services\Analyse\AnalyseIAService;
use App\Services\Analyse\QuotaAnalyseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function __construct(private AnalyseIAService $analyseIAService, private QuotaAnalyseService $quotaService,)
    {
       
    }

    public function create()
    {
        return view('analyses.create');
    }

    // lancerAnalyse() — nécessite un compte (consomme des crédits IA, cf. Module Détection/Analyse)
    public function store(StoreAnalyseRequest $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:texte,lien,numero,email,image'],
            'contenu' => ['required_without:fichier', 'nullable', 'string'],
            'fichier' => ['required_without:contenu', 'nullable', 'file', 'image', 'max:5120'],
        ]);
         if ($this->quotaService->quotaAtteint($request->user())) {
        return back()->withErrors([
            'type' => "Quota quotidien d'analyses IA atteint. Réessayez demain.",
        ]);
    }

        // Si image : le service se charge en interne du passage par le Module OCR
        // avant d'appeler l'IA (chaîne include Extraire le texte -> Envoyer à l'IA)
        $contenu = $data['contenu'] ?? $request->file('fichier')->path();

        [$score, $conclusion] = $this->analyseIAService->analyser($data['type'], $contenu);

        // mettreAJourScore() est appliqué ici, au retour de l'appel IA
        $analyse = $request->user()->analyses()->create([
            'date_analyse' => now(),
            'score_fiabilite' => $score,
            'conclusion' => $conclusion,
        ]);

        return redirect()->route('analyses.show', $analyse);
    }

    public function show(Analyse $analyse)
    {
        // Vérifie que l'analyse appartient bien à l'utilisateur courant
        $this->authorize('view', $analyse);

        // afficher des conseils : <<extend>> de Analyser un contenu, calculé simplement
        // à partir du score plutôt que par un nouvel appel IA (aucun coût supplémentaire)
        $conseil = match (true) {
            $analyse->score_fiabilite >= 70 => 'Ce contenu présente de forts indices d\'arnaque. Ne partagez aucune information personnelle.',
            $analyse->score_fiabilite >= 40 => 'Ce contenu est ambigu. Vérifiez la source avant d\'agir.',
            default => 'Aucun indice fort détecté, restez tout de même prudent.',
        };

        return view('analyses.show', compact('analyse', 'conseil'));
    }

    // genererRapport() — <<extend>> de Consulter l'historique / Suivre un signalement
    public function genererRapport(Analyse $analyse)
    {
        $this->authorize('view', $analyse);

        return view('analyses.rapport', compact('analyse'));
    }
}