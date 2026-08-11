<?php

namespace App\Http\Controllers;

use App\Models\Analyse;
use App\Services\Analyse\AnalyseIAService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function __construct(private AnalyseIAService $analyseIAService)
    {
        // Laravel injecte automatiquement l'implémentation liée dans AppServiceProvider
    }

    public function create()
    {
        return view('analyses.create');
    }

    // lancerAnalyse() — nécessite un compte (consomme des crédits IA, cf. Module Détection/Analyse)
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:texte,lien,numero,email,image'],
            'contenu' => ['required_without:fichier', 'nullable', 'string'],
            'fichier' => ['required_without:contenu', 'nullable', 'file', 'image', 'max:5120'],
        ]);

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

        // Génération PDF à implémenter (ex. barryvdh/laravel-dompdf) — non détaillée ici,
        // hors périmètre "contrôleur", relève d'une bibliothèque tierce à choisir avec vous.
        return view('analyses.rapport', compact('analyse'));
    }
}