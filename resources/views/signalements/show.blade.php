@extends('layouts.app')

@section('title', 'Détail du signalement - SENTINEL IA')

@section('content')
<div class="container fade-in" style="max-width: 900px;">
    <div class="card" style="margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-glow); padding-bottom: 15px; margin-bottom: 15px;">
            <h1 class="page-title" style="margin: 0; font-size: 1.8rem;">Dossier du {{ $signalement->created_at->format('d/m/Y à H:i') }}</h1>
            <span style="padding: 5px 15px; border-radius: 20px; font-weight: bold; border: 1px solid currentColor; color: {{ $signalement->statut->value == 'valide' ? 'var(--success)' : ($signalement->statut->value == 'rejete' ? 'var(--danger)' : 'var(--warning)') }};">
                {{ $signalement->statut->label() }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <p class="text-secondary">Entité visée :</p>
                <p style="font-size: 1.2rem; font-weight: bold;">{{ ucfirst($signalement->entiteSuspecte->type) }} - {{ $signalement->entiteSuspecte->valeur }}</p>
            </div>
            <div>
                <p class="text-secondary">Lieu :</p>
                <p>{{ $signalement->ville ?? 'Non renseigné' }}</p>
            </div>
            <div style="grid-column: 1 / -1;">
                <p class="text-secondary">Description :</p>
                <div style="background: var(--bg-surface); padding: 15px; border-radius: 8px;">
                    {{ $signalement->description }}
                </div>
            </div>
            <div style="grid-column: 1 / -1;">
                <p class="text-secondary">Traité par :</p>
                <p>{{ $signalement->moderateur ? $signalement->moderateur->nom . ' ' . $signalement->moderateur->prenom : 'Pas encore traité' }}</p>
            </div>
        </div>
    </div>

    @if(Auth::user()->estModerateur())
        <!-- Actions du Modérateur : Approuver / Rejeter (selon le diagramme de classe : VerifierSignalement / SupprimerFauxSignalement) -->
        <div class="card border-glow" style="border-color: var(--border-glow); padding: 25px;">
            <h2 style="font-size: 1.3rem; margin-bottom: 10px; color: var(--text-primary);">Décision de modération</h2>
            
            @if($signalement->statut === \App\Enums\StatutSignalement::EN_ATTENTE)
                <p class="text-secondary" style="margin-bottom: 20px; font-size: 0.95rem;">
                    Examinez les faits signalés ci-dessus pour approuver ou rejeter ce signalement dans la base nationale :
                </p>
                <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
                    <form action="{{ route('moderation.valider', $signalement) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-primary" style="background: var(--success); color: #000; border-color: var(--success);">
                            Approuver le signalement
                        </button>
                    </form>

                    <form action="{{ route('moderation.rejeter', $signalement) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-signal">
                            Rejeter le signalement
                        </button>
                    </form>

                    <a href="{{ route('moderation.index') }}" class="btn btn-secondary">
                        Retour à la file d'attente
                    </a>
                </div>
            @else
                <p class="text-secondary" style="margin-bottom: 15px; font-size: 0.95rem;">
                    Ce dossier a déjà été traité avec le statut <strong style="color: {{ $signalement->statut->color() }};">{{ $signalement->statut->label() }}</strong>.
                </p>
                <a href="{{ route('moderation.index') }}" class="btn btn-secondary">
                    Retour à la file d'attente
                </a>
            @endif
        </div>
    @else
        <!-- Section Preuves : Réservée au citoyen déclarant (cas d'utilisation Signaler une arnaque <<include>> Fournir preuve) -->
        <h2 style="margin-bottom: 20px;">Preuves ({{ $signalement->preuves->count() }})</h2>
        
        <div class="features-grid" style="margin-bottom: 30px;">
            @forelse($signalement->preuves as $preuve)
                <div class="card" style="padding: 15px;">
                    <p style="font-weight: bold; color: var(--blue-glow);">{{ ucfirst($preuve->type) }}</p>
                    <p class="text-secondary" style="font-size: 0.85rem;">Document chiffré stocké sur les serveurs.</p>
                </div>
            @empty
                <div class="card" style="grid-column: 1 / -1; padding: 20px; text-align: center;">
                    <p class="text-secondary" style="margin: 0;">Aucune preuve jointe à ce signalement.</p>
                </div>
            @endforelse
        </div>

        @if($signalement->statut === \App\Enums\StatutSignalement::EN_ATTENTE && Auth::id() === $signalement->user_id)
            <!-- Formulaire d'ajout de preuve (uniquement pour l'auteur du signalement tant qu'il est en attente) -->
            <div class="card border-glow" style="border-color: var(--blue-shield);">
                <h3 style="margin-bottom: 10px;">Ajouter une preuve</h3>
                <form action="{{ route('signalements.preuves.store', $signalement) }}" method="POST" enctype="multipart/form-data" style="margin-top: 15px;">
                    @csrf
                    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 15px; align-items: end;">
                        <div class="form-group" style="margin: 0;">
                            <label>Type de preuve</label>
                            <select name="type" required class="form-control">
                                <option value="image">Image (Capture)</option>
                                <option value="document">Document (PDF)</option>
                                <option value="lien">Lien web (TXT)</option>
                            </select>
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label>Fichier</label>
                            <input type="file" name="fichier" required class="form-control" style="padding: 9px;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" style="margin-top: 15px;">Envoyer la preuve</button>
                </form>
            </div>
        @endif
    @endif
</div>
@endsection