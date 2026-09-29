@extends('layouts.app')

@section('title', 'Consommation IA - SENTINEL IA')

@section('content')
<div class="stats-page">
    <h1 class="page-title">Consommation IA</h1>
    <p>Tokens utilisés par les analyses et les reformulations, par jour et par modèle. Le suivi commence à son activation.</p>
    <form method="GET" class="form-group">
        <label for="jours">Nombre de jours</label>
        <input id="jours" name="jours" type="number" min="1" max="365" value="{{ $jours }}" required>
        <button class="btn btn-primary" type="submit">Afficher</button>
    </form>
    <div class="stats-metrics">
        <article class="stats-metric"><span>Appels mesurés</span><strong>{{ number_format($lignes->sum('appels'), 0, ',', ' ') }}</strong></article>
        <article class="stats-metric"><span>Total tokens</span><strong>{{ number_format($lignes->sum(fn ($l) => $l->entrees + $l->ecrits + $l->lus + $l->sorties), 0, ',', ' ') }}</strong></article>
        <article class="stats-metric"><span>Tokens lus dans le cache</span><strong>{{ number_format($lignes->sum('lus'), 0, ',', ' ') }}</strong></article>
    </div>
    <div class="stats-card" style="overflow-x: auto;">
        <table class="table" style="width: 100%; text-align: left; border-spacing: 12px;">
            <caption>Historique quotidien des tokens</caption>
            <thead><tr><th scope="col">Jour</th><th scope="col">Modèle</th><th scope="col">Appels</th><th scope="col">Entrée hors cache</th><th scope="col">Écrits en cache</th><th scope="col">Lus du cache</th><th scope="col">Sortie</th></tr></thead>
            <tbody>
            @forelse($lignes as $ligne)
                <tr><td>{{ $ligne->jour }}</td><td>{{ $ligne->modele }}</td><td>{{ $ligne->appels }}</td><td>{{ $ligne->entrees }}</td><td>{{ $ligne->ecrits }}</td><td>{{ $ligne->lus }}</td><td>{{ $ligne->sorties }}</td></tr>
            @empty
                <tr><td colspan="7">Aucune consommation enregistrée sur cette période.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <p class="text-secondary">Le cache réduit le traitement d’un préfixe identique réutilisé. Avec Haiku 4.5, moins de 4 096 tokens éligibles donnent normalement zéro token en cache. Les compteurs incluent les réponses reçues même si leur contenu est ensuite refusé par l’application ; les appels sans mesure fournie par Claude ne sont pas comptabilisés.</p>
    <p class="text-secondary">Le total de tokens n’est pas un montant facturé. Consultez la console Anthropic pour la facturation complète.</p>
</div>
@endsection
