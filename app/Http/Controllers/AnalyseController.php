<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAnalyseRequest;
use App\Models\Analyse;
use App\Services\Analyse\AnalyseIAIndisponibleException;
use App\Services\Analyse\AnalyseIAService;
use App\Services\Analyse\ContenuLienFetcher;
use App\Services\Analyse\QuotaAnalyseService;
use Illuminate\Http\RedirectResponse;

class AnalyseController extends Controller
{
    public function __construct(
        private AnalyseIAService $analyseIAService,
        private QuotaAnalyseService $quotaService,
        private ContenuLienFetcher $contenuLienFetcher,
    ) {}

    public function create()
    {
        return view('analyses.create');
    }

    // lancerAnalyse() — nécessite un compte (consomme des crédits IA, cf. Module Détection/Analyse)
    public function store(StoreAnalyseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        if ($this->quotaService->quotaAtteint($request->user())) {
            return back()->withErrors([
                'type' => "Quota quotidien d'analyses IA atteint. Réessayez demain.",
            ]);
        }

        // L’image validée est envoyée directement à la vision de Claude.
        $contenu = $data['type'] === 'image'
            ? $request->file('fichier')->path()
            : $data['contenu'];

        $sourceWeb = null;
        if ($data['type'] === 'lien') {
            $contenuRecupere = $this->contenuLienFetcher->recuperer($contenu);
            if ($contenuRecupere === null) {
                return back()->withInput()->withErrors([
                    'contenu' => 'Le contenu de cette page n’a pas pu être récupéré. Vérifiez le domaine ou l’URL ; si le site bloque l’accès ou nécessite JavaScript, envoyez une capture d’écran ou copiez son texte. Aucune analyse IA n’a été facturée.',
                ]);
            }
            $sourceWeb = $this->contenuLienFetcher->source();
            $contenu = "URL soumise : {$contenu}\n\n{$contenuRecupere}";
        }
        try {
            [$score, $conclusion] = $this->analyseIAService->analyser($data['type'], $contenu);
        } catch (AnalyseIAIndisponibleException) {
            return back()
                ->withInput()
                ->withErrors([
                    'ia' => 'L’analyse IA est indisponible. Aucun mode dégradé n’a été utilisé. Réessayez plus tard.',
                ]);
        }

        // mettreAJourScore() est appliqué ici, au retour de l'appel IA
        $analyse = $request->user()->analyses()->create([
            'type' => $data['type'],
            'source_web' => $sourceWeb,
            'date_analyse' => now(),
            'score_fiabilite' => $score,
            'conclusion' => $conclusion,
            'api_appel_effectue' => $this->analyseIAService->appelDistantEffectue(),
        ]);

        return redirect()->route('analyses.show', $analyse);
    }

    public function show(Analyse $analyse)
    {
        // Vérifie que l'analyse appartient bien à l'utilisateur courant
        $this->authorize('view', $analyse);

        // afficher des conseils : <<extend>> de Analyser un contenu, calculé simplement
        // à partir du score plutôt que par un nouvel appel IA (aucun coût supplémentaire)
        $risqueEleve = (float) $analyse->score_fiabilite >= config('sentinel_ia.seuil_risque_eleve', 70);
        $risqueModere = (float) $analyse->score_fiabilite >= config('sentinel_ia.seuil_risque_modere', 40);
        $scoreClass = $risqueEleve ? 'danger' : ($risqueModere ? 'warning' : 'safe');

        $conseil = match (true) {
            $risqueEleve => 'Ce contenu présente de forts indices d\'arnaque. Ne partagez aucune information personnelle.',
            $risqueModere => 'Ce contenu est ambigu. Vérifiez la source avant d\'agir.',
            default => 'Aucun indice fort détecté, restez tout de même prudent.',
        };

        return view('analyses.show', compact('analyse', 'conseil', 'risqueEleve', 'scoreClass'));
    }
}
