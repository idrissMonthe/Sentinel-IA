@extends('layouts.app')

@section('title', 'Rédiger une alerte - SENTINEL IA')

@section('content')
<div class="analysis-container fade-in">
    <h1 class="page-title" style="color: var(--warning);">Diffuser une alerte officielle</h1>
    
    <div class="card" style="margin-top: 20px;">
        <form action="{{ route('alertes.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="titre">Titre de l'alerte</label>
                <input type="text" id="titre" name="titre" class="form-control" value="{{ old('titre') }}" required placeholder="Ex: Nouvelle vague de phishing Mobile Money">
            </div>

            <div class="form-group">
                <label for="contenu">Contenu détaillé</label>
                <textarea id="contenu" name="contenu" class="form-control" rows="8" required placeholder="Expliquez la menace et les précautions à prendre...">{{ old('contenu') }}</textarea>
            </div>

            <div class="form-group" style="background: var(--bg-surface); padding: 15px; border-radius: 8px;">
                <label style="margin-bottom: 10px; display: block;">Statut de publication</label>
                <div style="display: flex; gap: 20px;">
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="action" value="publier" {{ old('action') == 'publier' ? 'checked' : '' }} required>
                        <span style="color: var(--success); font-weight: bold;">Publier (Visible par tous immédiatement)</span>
                    </label>
                    <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                        <input type="radio" name="action" value="brouillon" {{ old('action') == 'brouillon' ? 'checked' : '' }}>
                        <span style="color: var(--text-secondary);">Enregistrer en brouillon</span>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn" style="background: var(--warning); color: #000; width: 100%; margin-top: 10px;">Enregistrer l'alerte</button>
        </form>
    </div>
</div>
@endsection