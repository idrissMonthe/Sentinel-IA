@extends('layouts.app')

@section('title', 'Résultat de l\'analyse - SENTINEL IA')

@section('content')
<div class="result-container fade-in">
    <div class="result-header text-center">
        <h1 class="page-title">Rapport de détection</h1>
        <p class="text-secondary">Analyse effectuée le {{ $analyse->date_analyse->format('d/m/Y à H:i') }}</p>
    </div>

    <div class="result-grid">
        <!-- Carte principale avec le score -->
        <div class="result-card main-score fade-in-element delay-1">
            <h2>Score de fiabilité</h2>
            
            <!-- Affichage visuel du score (Couleur conditionnelle selon le danger) -->
            @php
                $scoreClass = $analyse->score_fiabilite > 70 ? 'danger' : ($analyse->score_fiabilite > 40 ? 'warning' : 'safe');
            @endphp
            
            <div class="score-circle {{ $scoreClass }}">
                <span class="score-number">{{ $analyse->score_fiabilite }}%</span>
                <span class="score-label">Risque</span>
            </div>

            <div class="conclusion-box">
                <h3>Conclusion de l'IA :</h3>
                <p>{{ $analyse->conclusion }}</p>
            </div>
        </div>

        <!-- Carte avec le conseil et les actions -->
        <div class="result-card actions-card fade-in-element delay-2">
            <h2>Recommandation</h2>
            <div class="conseil-box">
                <p><strong>Que faire maintenant ?</strong></p>
                <p>{{ $conseil }}</p>
            </div>

            <div class="action-buttons">
                <!-- Lien pré-rempli vers la création d'un signalement (Cas A du cahier des charges) -->
                <a href="{{ route('signalements.create') . '?analyse_id=' . $analyse->id }}" class="btn btn-signal btn-block">
                    🚨 Signaler cette arnaque
                </a>

                <!-- Lien pour générer la vue imprimable -->
                <a href="{{ route('analyses.rapport', $analyse) }}" class="btn btn-secondary btn-block" style="margin-top: 15px;">
                    📄 Générer un rapport
                </a>
            </div>
        </div>
    </div>
</div>
@endsection