@extends('layouts.app')

@section('title', 'Statistiques de la plateforme - SENTINEL IA')

@section('content')
<div class="stats-page fade-in">
    <header class="stats-header">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Les chiffres du réseau</span>
        <h1 class="page-title">L’impact de SENTINEL IA</h1>
        <p class="text-secondary">Ce que la vigilance collective permet de rendre visible.</p>
    </header>

    <section class="stats-metrics" aria-label="Indicateurs principaux">
        <article class="stats-metric stats-metric-cyan">
            <span class="stats-metric-label">Analyses IA effectuées</span>
            <strong>{{ $statistiques->analyses_effectuees }}</strong>
            <span class="stats-metric-note">contenus examinés</span>
        </article>
        <article class="stats-metric stats-metric-coral">
            <span class="stats-metric-label">Arnaques confirmées</span>
            <strong>{{ $statistiques->total_signalement_actifs }}</strong>
            <span class="stats-metric-note">signalements actifs</span>
        </article>
        <article class="stats-metric stats-metric-gold">
            <span class="stats-metric-label">Utilisateurs protégés</span>
            <strong>{{ $statistiques->utilisateurs_proteges }}</strong>
            <span class="stats-metric-note">membres du réseau</span>
        </article>
    </section>

    <section class="stats-detail-grid" aria-label="Détails des signalements">
        <article class="stats-card">
            <div class="stats-card-heading"><span class="stats-card-kicker">Répartition</span><h2>Types de menaces les plus signalés</h2></div>
            <ul class="stats-list">
                @forelse($statistiques->menaces_frequentes ?? [] as $menace)
                    <li><span>{{ $menace['label'] }}</span><strong>{{ $menace['total'] }} entités</strong></li>
                @empty
                    <li class="stats-empty">Données insuffisantes pour le moment.</li>
                @endforelse
            </ul>
        </article>

        <article class="stats-card">
            <div class="stats-card-heading"><span class="stats-card-kicker">Géographie</span><h2>Zones les plus touchées</h2></div>
            <ul class="stats-list">
                @forelse($statistiques->zones_touchees ?? [] as $zone)
                    <li><span>{{ $zone['label'] ?: 'Non renseigné' }}</span><strong>{{ $zone['total'] }} cas</strong></li>
                @empty
                    <li class="stats-empty">Données insuffisantes pour le moment.</li>
                @endforelse
            </ul>
        </article>
    </section>
</div>
@endsection
