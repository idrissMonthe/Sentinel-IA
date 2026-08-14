@extends('layouts.app')

@section('title', 'Statistiques de la plateforme - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <div class="text-center" style="margin-bottom: 40px;">
        <h1 class="page-title" style="color: var(--blue-glow);">L'impact de SENTINEL IA</h1>
        <p class="text-secondary">Les chiffres en temps réel de notre lutte contre la cybercriminalité.</p>
    </div>

    <!-- Top Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div class="card text-center" style="border-top: 4px solid var(--blue-glow);">
            <h3 class="text-secondary" style="font-size: 1.1rem;">Analyses IA effectuées</h3>
            <div style="font-size: 3rem; font-weight: bold; color: var(--text-primary); margin: 10px 0;">{{ $totalAnalyses ?? '0' }}</div>
        </div>
        <div class="card text-center" style="border-top: 4px solid var(--success);">
            <h3 class="text-secondary" style="font-size: 1.1rem;">Arnaques confirmées</h3>
            <div style="font-size: 3rem; font-weight: bold; color: var(--success); margin: 10px 0;">{{ $totalSignalementsValides ?? '0' }}</div>
        </div>
        <div class="card text-center" style="border-top: 4px solid var(--warning);">
            <h3 class="text-secondary" style="font-size: 1.1rem;">Utilisateurs protégés</h3>
            <div style="font-size: 3rem; font-weight: bold; color: var(--warning); margin: 10px 0;">{{ $totalUtilisateurs ?? '0' }}</div>
        </div>
    </div>

    <!-- Section Détails -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 40px;">
        <!-- Top Types d'arnaques -->
        <div class="card">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-glow); padding-bottom: 10px;">Types de menaces les plus signalés</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                @forelse($topTypes as $type => $count)
                    <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="font-weight: bold; color: var(--text-secondary);">{{ ucfirst($type) }}</span>
                        <span style="color: var(--danger); font-weight: bold;">{{ $count }} signalements</span>
                    </li>
                @empty
                    <li class="text-secondary">Données insuffisantes pour le moment.</li>
                @endforelse
            </ul>
        </div>

        <!-- Top Villes (Si tu as implémenté le suivi géo) -->
        <div class="card">
            <h3 style="margin-bottom: 20px; border-bottom: 1px solid var(--border-glow); padding-bottom: 10px;">Zones les plus touchées</h3>
            <ul style="list-style: none; padding: 0; margin: 0;">
                @forelse($topVilles as $ville => $count)
                    <li style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <span style="font-weight: bold; color: var(--text-secondary);">{{ $ville ?: 'Non renseigné' }}</span>
                        <span style="color: var(--warning); font-weight: bold;">{{ $count }} cas</span>
                    </li>
                @empty
                    <li class="text-secondary">Données insuffisantes pour le moment.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection