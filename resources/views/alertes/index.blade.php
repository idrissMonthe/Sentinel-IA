@extends('layouts.app')

@section('title', 'Alertes en cours - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <div class="hero-section" style="padding: 30px 20px; border-color: var(--warning);">
        <h1 style="color: var(--warning);">Alertes de Sécurité</h1>
        <p>Restez informé des dernières vagues d'arnaques identifiées par la modération.</p>
    </div>

    <div style="margin-top: 40px; display: flex; flex-direction: column; gap: 20px;">
        @forelse ($alertes as $alerte)
            <div class="card fade-in-element" style="border-left: 4px solid var(--warning);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                    <h2 style="margin: 0; font-size: 1.4rem; color: var(--warning);">{{ $alerte->titre }}</h2>
                    <span class="text-secondary" style="font-size: 0.9rem;">
                        Publié le {{ $alerte->created_at->format('d/m/Y à H:i') }} par {{ $alerte->auteur?->prenom ?? $alerte->auteur?->nom ?? 'L\'équipe SENTINEL' }}
                    </span>
                </div>
                <div style="color: var(--text-primary); line-height: 1.6; white-space: pre-line;">
                    {{ $alerte->contenu }}
                </div>
            </div>
        @empty
            <div class="card text-center" style="padding: 40px;">
                <p style="font-size: 1.2rem; color: var(--success);">Aucune alerte majeure en cours. La situation est calme !</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $alertes->links() }}
    </div>
</div>
@endsection