@extends('layouts.app')
@section('title', 'Accueil - SENTINEL IA')

@section('content')
<section class="hero-section" aria-labelledby="hero-title">
    <div class="hero-copy">
        <span class="eyebrow"><span class="eyebrow-dot"></span> Sentinel IA · le réflexe quand quelque chose cloche</span>
        <h1 id="hero-title">Quand un message<br><em>sonne faux.</em></h1>
        <p>Une vérification rapide, des indices lisibles et une communauté attentive pour ne pas laisser le doute décider à votre place.</p>
    </div>
    <div class="hero-actions">
        @auth
            <a href="{{ route('analyses.create') }}" class="btn btn-primary">Vérifier un contenu</a>
            <a href="{{ route('signalements.create') }}" class="btn btn-signal">Raconter ce qui s’est passé</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">Entrer dans le réseau</a>
            <a href="{{ route('base-collaborative.index') }}" class="btn btn-logout">Voir les signalements</a>
        @endauth
    </div>
    <div class="hero-orbit hero-orbit-one"></div><div class="hero-orbit hero-orbit-two"></div>
    <div class="hero-insight glass-panel"><span class="insight-pulse"></span><div><strong>Le doute est un signal</strong><small>Quelques secondes peuvent changer la suite.</small></div></div>
</section>

<section class="home-section" aria-labelledby="features-title">
    <div class="section-heading"><div><span class="eyebrow">Les bons réflexes, au bon moment</span><h2 id="features-title">Pas besoin d’être expert pour flairer le piège.</h2></div><p>Trois portes d’entrée pour comprendre ce qui se cache derrière un numéro, un lien ou une promesse trop belle.</p></div>
    <div class="features-grid">
        <article class="card feature-card"><span class="feature-index">01</span><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C6.58 2 2 6.58 2 12s4.58 10 10 10 10-4.58 10-10S17.42 2 12 2m0 18c-4.34 0-8-3.66-8-8s3.66-8 8-8c1.82 0 3.51.65 4.87 1.71l-1.41 1.41A5.9 5.9 0 0 0 12 6c-1.58 0-3.09.63-4.23 1.77S6 10.41 6 12s.63 3.09 1.77 4.23S10.41 18 12 18s3.09-.63 4.23-1.77l-1.41-1.41c-1.53 1.53-4.1 1.53-5.63 0-.76-.76-1.18-1.76-1.18-2.82s.42-2.05 1.18-2.82c1.28-1.28 3.3-1.47 4.82-.6l-1.49 1.49c-.16-.05-.33-.08-.51-.08-1.08 0-2 .92-2 2s.92 2 2 2 2-.92 2-2c0-.18-.03-.34-.08-.51l4.36-4.36C19.36 8.48 20 10.17 20 11.99c0 4.34-3.66 8-8 8Z"/></svg><h3>Décoder le doute</h3><p>Soumettez un SMS, une image, un lien ou un numéro et obtenez des indices concrets sur son niveau de risque.</p></article>
        <article class="card feature-card"><span class="feature-index">02</span><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 2H2v19c0 .55.45 1 1 1h19v-2H4zM19 18c.55 0 1-.45 1-1V5c0-.55-.45-1-1-1h-4c-.55 0-1 1-1 1v12c0 .55.45 1 1 1zm-3-12h2v10h-2zm-5 12c.55 0 1-.45 1-1v-7c0-.55-.45-1-1-1H7c-.55 0-1 1-1 1v7c0 .55.45 1 1 1zm-3-7h2v5H8z"/></svg><h3>Regarder derrière le lien</h3><p>Comparez une adresse, un email ou un numéro avec les signalements vérifiés de la communauté.</p></article>
        <article class="card feature-card"><span class="feature-index">03</span><svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M16.13 9.64c-.17-.95-1-1.64-1.97-1.64H9.83c-.97 0-1.79.69-1.97 1.64L6.16 19H3.99v2h16v-2h-2.17l-1.7-9.36ZM8.19 19l1.64-9h4.33l1.64 9H8.2ZM11 4.5V6h2V3h-2zm8 6.5v2h3v-2zM5 12v-1H2v2h3z"/></svg><h3>Faire circuler l’alerte</h3><p>Un signalement bien documenté peut éviter la prochaine victime. Partagez ce que vous avez vu.</p></article>
    </div>
</section>

<section class="home-proof glass-panel" aria-label="Engagement Sentinel IA"><div class="proof-mark">✦</div><div><strong>Le meilleur moment pour vérifier, c’est avant de cliquer.</strong><p>Sentinel IA transforme une intuition en information utile.</p></div><a href="{{ route('base-collaborative.index') }}" class="text-link">Voir la base <span>↗</span></a></section>
@endsection
