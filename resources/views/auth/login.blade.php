@extends('layouts.app')

@section('title', 'Connexion - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title">Connexion</h2>
        <p class="auth-subtitle">Accédez à votre espace Sentinel IA</p>

        <!-- Formulaire pointant vers login.store avec la méthode POST -->
        <form action="{{ route('login.store') }}" method="POST" class="auth-form">
            @csrf

            <!-- Champ Email -->
            <div class="form-group fade-in-element delay-2">
                <label for="email">Adresse Email</label>
                <!-- Utilisation de old() pour garder l'email en cas d'erreur de mot de passe -->
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="exemple@email.com">
            </div>

            <div class="form-group fade-in-element delay-3">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="Votre mot de passe">
            </div>

            <!-- Bouton de soumission -->
            <button type="submit" class="btn btn-primary btn-block fade-in-element delay-4">Se connecter</button>
        </form>

        <!-- Lien vers l'inscription -->
        <div class="auth-footer fade-in-element delay-5">
            <p>Nouveau sur la plateforme ? <a href="{{ route('register') }}">Créer un compte</a>.</p>
        </div>
    </div>
</div>
@endsection