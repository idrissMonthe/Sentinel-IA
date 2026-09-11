@extends('layouts.app')

@section('title', 'Page introuvable - SENTINEL IA')

@section('content')
<section class="error-page fade-in" aria-labelledby="error-title">
    <div class="error-page-code">404 · OUPS</div>
    <h1 id="error-title">Cette page n’est pas<br><em>au rendez-vous.</em></h1>
    <p>Cette adresse n’existe pas ou le contenu demandé a été déplacé.</p>
    <div class="error-page-actions">
        <a href="{{ route('accueil') }}" class="btn btn-primary">Retour à l’accueil</a>
        <a href="{{ route('base-collaborative.index') }}" class="btn btn-secondary">Rechercher une arnaque</a>
    </div>
</section>
@endsection
