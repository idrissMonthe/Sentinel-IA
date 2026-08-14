@extends('layouts.app')

@section('title', 'Mes signalements - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <h1 class="page-title">Mes Signalements</h1>
    
    <div class="features-grid" style="margin-top: 20px;">
        @forelse ($signalements as $signalement)
            <div class="card fade-in-element">
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <span class="text-secondary">{{ $signalement->created_at->format('d/m/Y') }}</span>
                    <!-- Remplacement dynamique du label de statut -->
                    <span style="font-weight: bold; color: {{ $signalement->statut->value == 'valide' ? 'var(--success)' : ($signalement->statut->value == 'rejete' ? 'var(--danger)' : 'var(--warning)') }};">
                        {{ $signalement->statut->label() }}
                    </span>
                </div>
                <p><strong>{{ ucfirst($signalement->entiteSuspecte->type ?? 'Entité') }} :</strong> {{ $signalement->entiteSuspecte->valeur ?? 'Inconnue' }}</p>
                <p class="text-secondary" style="font-size: 0.9rem; margin-top: 10px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ $signalement->description }}
                </p>
                <a href="{{ route('signalements.show', $signalement) }}" class="btn btn-secondary" style="border: 1px solid var(--border-glow); width: 100%; text-align: center; margin-top: 15px;">Voir le dossier</a>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center;">
                <p>Vous n'avez soumis aucun signalement.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $signalements->links() }}
    </div>
</div>
@endsection