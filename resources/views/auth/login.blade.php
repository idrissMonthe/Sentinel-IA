@extends('layouts.app')

@section('title', 'Connexion - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title"><svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24"  
fill="currentColor" viewBox="0 0 24 24" >
<!--Boxicons v3.0.8 https://boxicons.com | License  https://docs.boxicons.com/free-->
<path d="M12 12c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5m0-8c1.65 0 3 1.35 3 3s-1.35 3-3 3-3-1.35-3-3 1.35-3 3-3M4 22h16c.55 0 1-.45 1-1v-1c0-3.86-3.14-7-7-7h-4c-3.86 0-7 3.14-7 7v1c0 .55.45 1 1 1m6-7h4c2.76 0 5 2.24 5 5H5c0-2.76 2.24-5 5-5"></path>
</svg> Connexion</h2>
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