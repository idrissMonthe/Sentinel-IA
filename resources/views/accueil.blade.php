@extends('layouts.app')
@section('title', 'Accueil - SENTINEL IA')

@section('content')
<script src="https://unpkg.com/@boxicons/js@latest"></script>
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
        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
            fill="currentColor" viewBox="0 0 24 24" >
            <!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
            <path d="M12 2C6.58 2 2 6.58 2 12s4.58 10 10 10 10-4.58 10-10S17.42 2 12 2m0 18c-4.34 0-8-3.66-8-8s3.66-8 8-8c1.82 0 3.51.65 4.87 1.71l-1.41 1.41A5.9 5.9 0 0 0 12 6c-1.58 0-3.09.63-4.23 1.77S6 10.41 6 12s.63 3.09 1.77 4.23S10.41 18 12 18s3.09-.63 4.23-1.77l-1.41-1.41c-1.53 1.53-4.1 1.53-5.63 0-.76-.76-1.18-1.76-1.18-2.82s.42-2.05 1.18-2.82c1.28-1.28 3.3-1.47 4.82-.6l-1.49 1.49c-.16-.05-.33-.08-.51-.08-1.08 0-2 .92-2 2s.92 2 2 2 2-.92 2-2c0-.18-.03-.34-.08-.51l4.36-4.36C19.36 8.48 20 10.17 20 11.99c0 4.34-3.66 8-8 8Z"></path>
        </svg>
        <h3>Détection IA</h3>
        <p>Soumettez un SMS, une image ou un numéro pour obtenir instantanément un score de fiabilité calculé par intelligence artificielle.</p>
    </div>

    <div class="card">
        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
                fill="currentColor" viewBox="0 0 24 24" >
                <!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
                <path d="M4 2H2v19c0 .55.45 1 1 1h19v-2H4z"></path><path d="M19 18c.55 0 1-.45 1-1V5c0-.55-.45-1-1-1h-4c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1zM16 6h2v10h-2zm-5 12c.55 0 1-.45 1-1v-7c0-.55-.45-1-1-1H7c-.55 0-1 .45-1 1v7c0 .55.45 1 1 1zm-3-7h2v5H8z"></path>
        </svg><h3>Base Collaborative </h3>
        <p>Recherchez un numéro, un lien ou une adresse email dans notre base mise à jour par les signalements vérifiés de la communauté.</p>
    </div>

    <div class="card">
        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
            fill="currentColor" viewBox="0 0 24 24" >
            <!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
            <path d="M16.13 9.64c-.17-.95-1-1.64-1.97-1.64H9.83c-.97 0-1.79.69-1.97 1.64L6.16 19H3.99v2h16v-2h-2.17l-1.7-9.36ZM8.19 19l1.64-9h4.33l1.64 9H8.2ZM11 4.5V6h2V3h-2zm8 6.5v2h3v-2zM5 12v-1H2v2h3zm12.3-6.72-1.06 1.06.71.71.71.71 1.06-1.06 1.06-1.06-.71-.71-.71-.71zM5.64 4.22l-.71.71-.71.71L5.28 6.7l1.06 1.06.71-.71.71-.71L6.7 5.28z"></path>
        </svg>
        <h3>Alertes de Modération</h3>
        <p>Consultez les mises en garde officielles publiées par nos modérateurs sur les nouvelles vagues de fraudes (Mobile Money, fausses offres...).</p>
    </div>
</div>

@endsection