@extends('layouts.app')

@section('title', 'Accueil - SENTINEL IA')

@section('content')
<div class="hero-section">
    <h1>Protection et détection intelligente des arnaques</h1>
    <p>Analyse de contenus suspects par IA, base collaborative de signalements et alertes en temps réel pour le Cameroun.</p>
    
    <div class="hero-actions">
        @auth
            <a href="{{ route('analyses.create') }}" class="btn btn-primary">Analyser un contenu</a>
            <a href="{{ route('signalements.create') }}" class="btn btn-signal">Signaler une arnaque</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a>
            <a href="{{ route('base-collaborative.index') }}" class="btn btn-logout">Consulter la base</a>
        @endauth
    </div>
</div>

<div class="features-grid">
    <div class="card">
        <h3>🔍 Détection IA</h3>
        <p>Soumettez un SMS, une image ou un numéro pour obtenir instantanément un score de fiabilité calculé par intelligence artificielle.</p>
    </div>

    <div class="card">
        <h3>🛡️ Base Collaborative</h3>
        <p>Recherchez un numéro, un lien ou une adresse email dans notre base mise à jour par les signalements vérifiés de la communauté.</p>
    </div>

    <div class="card">
        <h3>📢 Alertes de Modération</h3>
        <p>Consultez les mises en garde officielles publiées par nos modérateurs sur les nouvelles vagues de fraudes (Mobile Money, fausses offres...).</p>
    </div>
</div>
@endsection