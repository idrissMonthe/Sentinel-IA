@extends('layouts.app')

@section('title', 'Signaler une arnaque - SENTINEL IA')

@section('content')
<div class="analysis-container fade-in">
    <div class="analysis-header text-center">
        <h1 class="page-title text-danger">Déclarer une arnaque</h1>
        <p class="text-secondary">Aidez la communauté en signalant une tentative de fraude.</p>
    </div>

    <!-- OPTIONNEL : Formulaire d'aide IA (Cas B) -->
    <details class="card" style="margin-bottom: 25px; border-color: var(--blue-glow); cursor: pointer;">
        <summary style="font-weight: bold; color: var(--green-ai); outline: none;">🤖 Besoin d'aide pour rédiger la description ? (Assistant IA)</summary>
        <div style="margin-top: 15px; border-top: 1px solid var(--border-glow); padding-top: 15px; cursor: default;">
            <form action="{{ route('signalements.suggestion-ia') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Type de contenu à résumer</label>
                    <select name="type" class="form-control">
                        <option value="texte">Texte</option>
                        <option value="lien">Lien</option>
                        <option value="numero">Numéro</option>
                        <option value="email">Email</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Contenu brut</label>
                    <textarea name="contenu" class="form-control" rows="3" placeholder="Collez ici le message ou l'échange brut..."></textarea>
                </div>
                <button type="submit" class="btn btn-secondary" style="border: 1px solid var(--blue-shield); background: transparent; color: var(--blue-shield);">Générer une suggestion de description</button>
            </form>
        </div>
    </details>

    <!-- FORMULAIRE PRINCIPAL (Cas A & B couverts par les old()) -->
    <div class="analysis-card fade-in-element delay-1">
        <form action="{{ route('signalements.store') }}" method="POST">
            @csrf
            
            <!-- Champ caché pour l'analyse_id -->
            <input type="hidden" name="analyse_id" value="{{ old('analyse_id', request()->query('analyse_id')) }}">

            <div class="form-group">
                <label for="type">Type d'entité frauduleuse</label>
                <select id="type" name="type" required class="form-control">
                    <option value="numero" {{ old('type') == 'numero' ? 'selected' : '' }}>Numéro de téléphone</option>
                    <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Adresse Email</option>
                    <option value="lien" {{ old('type') == 'lien' ? 'selected' : '' }}>Lien / Site Web</option>
                </select>
            </div>

            <div class="form-group">
                <label for="valeur">Numéro, Email ou Lien suspect</label>
                <input type="text" id="valeur" name="valeur" class="form-control" value="{{ old('valeur') }}" required>
            </div>

            <div class="form-group">
                <label for="ville">Ville (Optionnel)</label>
                <input type="text" id="ville" name="ville" class="form-control" value="{{ old('ville') }}" placeholder="Ex: Douala, Yaoundé...">
            </div>

            <div class="form-group">
                <label for="description">Description des faits</label>
                <textarea id="description" name="description" class="form-control" required rows="6" placeholder="Expliquez comment l'arnaque s'est déroulée...">{{ old('description') }}</textarea>
                <small class="text-secondary">Ce texte reste modifiable, vérifiez-le avant d'envoyer.</small>
            </div>

            <button type="submit" class="btn btn-signal btn-block">Soumettre le signalement</button>
        </form>
    </div>
</div>
@endsection