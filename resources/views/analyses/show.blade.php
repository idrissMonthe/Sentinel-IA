@extends('layouts.app')

@section('title', 'Résultat de l\'analyse - SENTINEL IA')

@section('content')
<div class="result-container fade-in">
    <div class="result-header text-center">
        <h1 class="page-title">Rapport de détection</h1>
        <p class="text-secondary">Analyse effectuée le {{ $analyse->date_analyse->format('d/m/Y à H:i') }}</p>
    </div>

    @if($analyse->source_web)
        <div class="conseil-box">
            <strong>Source utilisée :</strong> {{ $analyse->source_web['url_finale'] }}
            <p>Page récupérée et texte transmis à Claude ({{ $analyse->source_web['caracteres'] }} caractères).
            L’analyse porte sur cette page, sans exécuter JavaScript ni parcourir tout le site.</p>
        </div>
    @elseif($analyse->type === 'image')
        <p class="text-secondary">Analyse des éléments visibles de la capture par Claude.</p>
    @elseif($analyse->type === 'lien')
        <p class="text-secondary">Ancienne analyse : la récupération du contenu de la page n’a pas été enregistrée.</p>
    @endif

    <div class="result-grid result-grid-primary">
        <section class="result-card main-score fade-in-element delay-1" aria-labelledby="risk-title">
            <p class="result-kicker">Évaluation Sentinel IA</p>
            <h2 id="risk-title">Score de risque · {{ $niveauRisque }}</h2>
            <div class="score-circle {{ $scoreClass }}">
                <span class="score-number">{{ $analyse->score_fiabilite }}%</span>
                <span class="score-label">Indice de risque</span>
            </div>
            <p class="risk-summary">{{ $resumeRisque }}</p>
            <div class="conclusion-box result-conclusion">
                <h3>Synthèse de l’analyse</h3>
                <p>{{ $analyse->conclusion }}</p>
            </div>
        </section>

        <aside class="result-card actions-card fade-in-element delay-2" aria-labelledby="actions-title">
            <p class="result-kicker">Prochaine étape</p>
            <h2 id="actions-title">Que faire maintenant ?</h2>
            <div class="conseil-box">
                <p>{{ $conseil }}</p>
            </div>
            <ul class="safety-actions">
                @foreach($gestes as $geste)<li>{{ $geste }}</li>@endforeach
            </ul>
            <div class="action-buttons">
                <a href="{{ route('analyses.create') }}" class="btn btn-primary btn-block">Faire une autre analyse</a>
                @if($signalementAutorise)
                    <a href="{{ route('signalements.create') . '?analyse_id=' . $analyse->id }}" class="btn btn-signal btn-block">Signaler cette arnaque</a>
                @endif
                <a href="{{ route('profile.historique') }}" class="btn btn-secondary btn-block">Retour à mon historique</a>
            </div>
        </aside>
    </div>

    <section class="analysis-transparency fade-in-element delay-3" aria-labelledby="repere-title">
        <div><p class="result-kicker">Pour interpréter le rapport</p><h2 id="repere-title">Des repères, pas une décision automatique</h2></div>
        <p>Le score aide à prioriser votre vigilance à partir du contenu soumis. Vérifiez toujours l’identité de l’expéditeur et utilisez un canal officiel avant toute action sensible.</p>
    </section>
</div>
@endsection
