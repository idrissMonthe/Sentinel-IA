@extends('layouts.app')

@section('title', 'Signaler une arnaque - SENTINEL IA')

@section('content')
<div class="analysis-container fade-in" style="max-width: 900px; margin: 0 auto; padding: 40px 20px;">
    
    <div class="analysis-header text-center" style="margin-bottom: 30px;">
        <h1 class="page-title text-danger">Déclarer une arnaque</h1>
        <p class="text-secondary">Aidez la communauté en signalant une tentative de fraude.</p>
    </div>

    <!-- Alertes de statut et erreurs -->
    @if(session('status'))
        <div class="card" style="border-left: 4px solid var(--blue-glow); background: rgba(0, 212, 255, 0.05); margin-bottom: 25px; padding: 15px 20px;">
            <p style="color: var(--blue-glow); margin: 0; font-weight: bold;">Info : {{ session('status') }}</p>
        </div>
    @endif

    @if($errors->any())
        <div class="card" style="border-left: 4px solid var(--danger); background: rgba(220, 53, 69, 0.05); margin-bottom: 25px; padding: 15px 20px;">
            <ul style="color: var(--danger); margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- OPTIONNEL : Formulaire d'aide IA (Cas B) -->
    <details class="card" style="margin-bottom: 30px; border: 1px dashed var(--blue-glow); background: rgba(10, 15, 30, 0.6); cursor: pointer; padding: 15px 20px;">
        <summary style="font-size: 1.05rem; font-weight: bold; color: var(--blue-glow); outline: none;">
            Assistant IA : Besoin d'aide pour rédiger la description ?
        </summary>
        <div style="margin-top: 15px; border-top: 1px solid var(--border-glow); padding-top: 20px; cursor: default;">
            <p class="text-secondary" style="font-size: 0.9rem; margin-bottom: 15px;">
                Saisissez vos notes brutes ou le texte de l'arnaque ci-dessous : le système va structurer et clarifier votre description avant l'envoi (consomme 1 quota IA).
            </p>

            <form action="{{ route('signalements.suggestion-ia') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 20px; margin-bottom: 15px;">
                    <div class="form-group">
                        <label style="display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;">Type de contenu</label>
                        <select name="type" class="form-control" style="width: 100%; padding: 10px; border-radius: 6px; background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow);" required>
                            <option value="texte" {{ old('type') == 'texte' ? 'selected' : '' }}>Texte</option>
                            <option value="lien" {{ old('type') == 'lien' ? 'selected' : '' }}>Lien</option>
                            <option value="numero" {{ old('type') == 'numero' ? 'selected' : '' }}>Numéro</option>
                            <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Email</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label style="display: block; font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 5px;">Contenu brut</label>
                        <input type="text" name="contenu" class="form-control" placeholder="Collez ici le message ou l'échange brut..." value="{{ old('contenu') }}" style="width: 100%; padding: 10px; border-radius: 6px; background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow);" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-secondary" style="font-size: 0.85rem; padding: 8px 16px; border: 1px solid var(--blue-shield); background: transparent; color: var(--blue-shield);">
                    Générer une suggestion de description
                </button>
            </form>
        </div>
    </details>

    <!-- FORMULAIRE PRINCIPAL (Cas A & B couverts par les old()) -->
    <div class="analysis-card fade-in-element delay-1" style="padding: 30px;">
        <form action="{{ route('signalements.store') }}" method="POST">
            @csrf
            
            <!-- Champ caché pour l'analyse_id -->
            <input type="hidden" name="analyse_id" value="{{ old('analyse_id', request('analyse_id')) }}">

            <!-- Indicateur de liaison d'analyse IA -->
            @if(old('analyse_id') || request('analyse_id'))
                <div style="margin-bottom: 20px; padding: 12px 15px; background: rgba(40, 167, 69, 0.1); border: 1px solid var(--success); border-radius: 6px; font-size: 0.85rem; color: var(--success);">
                    Ce signalement sera automatiquement lié à l'analyse système #{{ old('analyse_id', request('analyse_id')) }}.
                </div>
            @endif

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div class="form-group">
                    <label for="type" style="display: block; margin-bottom: 8px; font-weight: bold; color: var(--text-primary);">Type d'entité frauduleuse *</label>
                    <select id="type" name="type" required class="form-control" style="width: 100%; padding: 12px; border-radius: 8px; background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow);">
                        <option value="numero" {{ old('type') == 'numero' ? 'selected' : '' }}>Numéro de téléphone</option>
                        <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Adresse Email</option>
                        <option value="lien" {{ old('type') == 'lien' ? 'selected' : '' }}>Lien / Site Web</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="valeur" style="display: block; margin-bottom: 8px; font-weight: bold; color: var(--text-primary);">Numéro, Email ou Lien suspect *</label>
                    <input type="text" id="valeur" name="valeur" class="form-control" value="{{ old('valeur') }}" placeholder="Ex: 677123456 ou scam@email.com" required style="width: 100%; padding: 12px; border-radius: 8px; background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow);">
                </div>
            </div>

            <div class="form-group" style="margin-bottom: 20px;">
                <label for="ville" style="display: block; margin-bottom: 8px; font-weight: bold; color: var(--text-primary);">Ville (Optionnel)</label>
                <input type="text" id="ville" name="ville" class="form-control" value="{{ old('ville') }}" placeholder="Ex: Douala, Yaoundé..." style="width: 100%; padding: 12px; border-radius: 8px; background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow);">
            </div>

            <div class="form-group" style="margin-bottom: 25px;">
                <label for="description" style="display: block; margin-bottom: 8px; font-weight: bold; color: var(--text-primary);">Description des faits *</label>
                <textarea id="description" name="description" class="form-control" required rows="6" placeholder="Expliquez comment l'arnaque s'est déroulée en détail..." style="width: 100%; padding: 12px; border-radius: 8px; background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow);">{{ old('description') }}</textarea>
                <small class="text-secondary" style="display: block; margin-top: 8px;">Ce texte reste modifiable, vérifiez-le avant d'envoyer.</small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 15px;">
                <a href="{{ route('signalements.index') }}" class="btn btn-secondary" style="padding: 12px 25px; border-radius: 8px;">Annuler</a>
                <button type="submit" class="btn btn-signal" style="padding: 12px 30px; font-weight: bold; border-radius: 8px;">Soumettre le signalement</button>
            </div>
        </form>
    </div>
</div>
@endsection