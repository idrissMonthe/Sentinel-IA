@extends('layouts.app')

@section('title', 'Gestion des Doublons - Modération')

@section('content')
<div class="container fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 class="page-title" style="color: var(--warning);">Fusion des Doublons</h1>
        <a href="{{ route('moderation.index') }}" class="btn btn-secondary" style="border: 1px solid var(--border-glow);">Retour à la file d'attente</a>
    </div>

    <p class="text-secondary" style="margin-bottom: 20px;">
        Le système a détecté des signalements portant sur la même entité (même numéro, email ou lien). Regroupez-les pour renforcer la fiabilité de la base de données.
    </p>

    <div style="display: flex; flex-direction: column; gap: 30px;">
        @forelse ($entitesDoublons as $entite)
            <div class="card" style="border-left: 4px solid var(--warning);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; border-bottom: 1px solid var(--border-glow); padding-bottom: 15px;">
                    <div>
                        <h2 style="margin: 0; font-size: 1.3rem;">{{ ucfirst($entite->type) }} : <span style="color: var(--warning);">{{ $entite->valeur }}</span></h2>
                        <span class="text-secondary" style="font-size: 0.9rem;">{{ $entite->signalements_count }} signalements en attente</span>
                    </div>
                    
                    <!-- Bouton pour fusionner d'un coup (nécessite une route dédiée dans ton Controller) -->
                    <form action="{{ route('moderation.valider', $entite->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn" style="background: var(--warning); color: #000; padding: 8px 16px;">Fusionner & Valider tout</button>
                    </form>
                </div>

                <!-- Liste des signalements sous ce doublon -->
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 15px;">
                    @foreach($entite->signalementsNonTraites as $signalement)
                        <div style="background: var(--bg-surface); padding: 15px; border-radius: 8px;">
                            <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 8px;">
                                Par {{ $signalement->user->prenom }} le {{ $signalement->created_at->format('d/m/Y') }}
                            </p>
                            <p style="font-size: 0.95rem; line-height: 1.4;">"{{ Str::limit($signalement->description, 100) }}"</p>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <div class="card text-center" style="padding: 50px;">
                <p style="font-size: 1.2rem; color: var(--success);">Aucun doublon suspect détecté par le système pour le moment.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection