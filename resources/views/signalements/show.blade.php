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

    <!-- Section Preuves -->
    <h2 style="margin-bottom: 20px;">Preuves ({{ $signalement->preuves->count() }})</h2>
    
    <div class="features-grid" style="margin-bottom: 30px;">
        @foreach($signalement->preuves as $preuve)
            <div class="card" style="padding: 15px;">
                <p style="font-weight: bold; color: var(--blue-glow);">{{ ucfirst($preuve->type) }}</p>
                <p class="text-secondary" style="font-size: 0.85rem;">Document chiffré stocké sur les serveurs.</p>
                <!-- Futur bouton de téléchargement -->
            </div>
        @endforeach
    </div>

    <!-- Formulaire d'ajout de preuve -->
    <div class="card border-glow" style="border-color: var(--blue-shield);">
        <h3>Ajouter une preuve</h3>
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
</div>
@endsection