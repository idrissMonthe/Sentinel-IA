@extends('layouts.app')

@section('title', 'Mentions légales - Sentinel IA')

@section('content')
<article class="legal-page fade-in">
    <p class="legal-kicker">Informations légales</p>
    <h1 class="page-title">Mentions légales</h1>
    <p class="legal-lead">Les présentes informations précisent le cadre d'utilisation de la plateforme Sentinel IA.</p>
    <section><h2>Éditeur</h2><p>Sentinel IA est une plateforme de sensibilisation, de détection et de signalement des arnaques numériques, éditée par MONTHE AHMED.</p></section>
    <section><h2>Objet du service</h2><p>La plateforme aide ses utilisateurs à identifier des contenus potentiellement frauduleux et à partager des signalements. Les résultats fournis sont indicatifs et ne remplacent ni un conseil juridique, ni une décision des autorités compétentes.</p></section>
    <section><h2>Responsabilité</h2><p>Chaque utilisateur est responsable des informations qu'il transmet. Sentinel IA s'efforce de maintenir le service accessible et les informations utiles, sans garantir l'absence d'erreur, d'interruption ou l'exhaustivité des contenus partagés par la communauté.</p></section>
    <section><h2>Propriété intellectuelle</h2><p>La structure, l'identité visuelle et les contenus propres à Sentinel IA sont protégés. Toute reproduction ou utilisation non autorisée est interdite, sauf accord préalable de l'éditeur.</p></section>
    <section><h2>Contact</h2><p>Pour toute question relative au service ou à ces mentions, écrivez à <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p></section>
</article>
@endsection
