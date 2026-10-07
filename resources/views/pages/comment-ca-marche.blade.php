@extends('layouts.app')

@section('title', 'Comment ça marche ? - Sentinel IA')

@section('content')
<div class="how-it-works-page fade-in">
    <header class="page-intro text-center">
        <p class="eyebrow">Un réflexe simple, une décision mieux informée</p>
        <h1 class="page-title">Comment Sentinel IA vous accompagne</h1>
        <p>En quelques étapes, transformez un doute en informations utiles et contribuez à protéger votre entourage.</p>
    </header>

    <div class="steps-grid">
        <article class="step-card fade-in-element delay-1">
            <span class="step-number">01</span>
            <h2>Soumettez ce qui vous interpelle</h2>
            <p>Collez un message, un lien, un numéro, une adresse e-mail ou une capture d’écran. Ne transmettez jamais de mot de passe ou de donnée bancaire.</p>
        </article>
        <article class="step-card fade-in-element delay-2">
            <span class="step-number">02</span>
            <h2>Comprenez le niveau de risque</h2>
            <p>Notre analyse fournit un score, une synthèse et des actions adaptées. Le résultat vous aide à décider, sans remplacer votre vigilance.</p>
        </article>
        <article class="step-card fade-in-element delay-3">
            <span class="step-number">03</span>
            <h2>Agissez utilement</h2>
            <p>Si une fraude est probable, signalez-la avec les éléments disponibles. La modération renforce ensuite la base collaborative.</p>
        </article>
    </div>

    <section class="how-it-works-note">
        <div><p class="eyebrow">À garder en tête</p><h2>Un score est un repère, pas une certitude.</h2><p>Vérifiez toujours l’identité d’un interlocuteur et privilégiez les canaux officiels pour toute opération sensible.</p></div>
        @auth
            <a href="{{ route('analyses.create') }}" class="btn btn-primary">Analyser un contenu</a>
        @else
            <a href="{{ route('register') }}" class="btn btn-primary">Créer un compte</a>
        @endauth
    </section>
</div>
@endsection
