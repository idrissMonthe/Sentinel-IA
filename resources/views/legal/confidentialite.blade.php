@extends('layouts.app')

@section('title', 'Confidentialité - Sentinel IA')

@section('content')
<article class="legal-page fade-in">
    <p class="legal-kicker">Vos données</p>
    <h1 class="page-title">Politique de confidentialité</h1>
    <p class="legal-lead">Nous limitons la collecte de données à ce qui est nécessaire au fonctionnement et à la sécurité de Sentinel IA.</p>
    <section><h2>Données traitées</h2><p>Lors de la création d'un compte, nous traitons notamment vos nom, prénom, adresse e-mail, numéro de téléphone facultatif et informations d'authentification. Les contenus que vous analysez ou signalez sont également traités pour fournir le service.</p></section>
    <section><h2>Utilisation</h2><p>Ces données permettent de gérer votre compte, sécuriser les accès, répondre à vos demandes et améliorer la détection des arnaques. Elles ne sont pas vendues à des tiers.</p></section>
    <section><h2>Conservation et suppression</h2><p>Vous pouvez demander la suppression de votre compte depuis vos paramètres. Afin de préserver l'intégrité de la base collaborative, certaines données liées aux signalements publiés peuvent être conservées sous une forme ne permettant plus l'accès à votre compte.</p></section>
    <section><h2>Vos choix</h2><p>Vous pouvez nous contacter pour toute question concernant vos données personnelles ou exercer vos droits à l'adresse <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.</p></section>
</article>
@endsection
