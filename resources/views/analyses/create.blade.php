@extends('layouts.app')

@section('title', 'Analyser un contenu - SENTINEL IA')

@section('content')
<div class="analysis-container fade-in">
    <div class="analysis-header text-center">
        <h1 class="page-title">Détection IA</h1>
        <p class="text-secondary">Soumettez un texte, un numéro, un lien, un email ou une image suspecte. Notre IA évaluera son niveau de dangerosité.</p>
    </div>

    <div class="analysis-card fade-in-element delay-1">
        <form action="{{ route('analyses.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Sélection du type de contenu -->
            <div class="form-group">
                <label for="type">Que souhaitez-vous analyser ?</label>
                <select id="type" name="type" required class="form-control">
                    <option value="texte" {{ old('type') == 'texte' ? 'selected' : '' }}>Texte (SMS, Message, etc.)</option>
                    <option value="lien" {{ old('type') == 'lien' ? 'selected' : '' }}>Lien / URL</option>
                    <option value="numero" {{ old('type') == 'numero' ? 'selected' : '' }}>Numéro de téléphone</option>
                    <option value="email" {{ old('type') == 'email' ? 'selected' : '' }}>Adresse Email</option>
                    <option value="image" {{ old('type') == 'image' ? 'selected' : '' }}>Image (Capture d'écran)</option>
                </select>
            </div>

            <!-- Champ texte (masqué si image est sélectionné) -->
            <div class="form-group" id="contenu-group">
                <label for="contenu">Contenu suspect</label>
                <textarea id="contenu" name="contenu" rows="5" class="form-control" placeholder="Collez le texte, le lien ou le numéro ici...">{{ old('contenu') }}</textarea>
            </div>

            <!-- Champ fichier (masqué par défaut) -->
            <div class="form-group" id="fichier-group" style="display: none;">
                <label for="fichier">Importer une image</label>
                <input type="file" id="fichier" name="fichier" accept="image/*" class="form-control">
                <small class="text-secondary">Formats acceptés : JPG, PNG, JPEG.</small>
            </div>

            <button type="submit" class="btn btn-primary btn-block" style="margin-top: 20px;">
                Lancer l'analyse
            </button>
        </form>
    </div>
</div>

<!-- Script Vanilla JS pour basculer l'affichage -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const typeSelect = document.getElementById('type');
        const contenuGroup = document.getElementById('contenu-group');
        const fichierGroup = document.getElementById('fichier-group');

        function toggleFields() {
            if (typeSelect.value === 'image') {
                contenuGroup.style.display = 'none';
                fichierGroup.style.display = 'block';
            } else {
                contenuGroup.style.display = 'block';
                fichierGroup.style.display = 'none';
            }
        }

        // Exécuter au changement
        typeSelect.addEventListener('change', toggleFields);
        
        // Exécuter au chargement (pour conserver l'état si old() a réinitialisé la page suite à une erreur)
        toggleFields();
    });
</script>
@endsection