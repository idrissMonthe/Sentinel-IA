@extends('layouts.app')

@section('title', 'Inscription - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title">Créer un compte</h2>
        <p class="auth-subtitle">Rejoignez le réseau Sentinel IA</p>

        @if ($errors->any())
            <div class="alert alert-danger fade-in-element delay-1">
                Veuillez corriger les erreurs ci-dessous.
            </div>
        @endif

        <form action="{{ route('register.store') }}" method="POST" class="auth-form">
            @csrf

            <div class="form-group fade-in-element delay-2">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required placeholder="Ex: Monthe">
                @error('nom') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group fade-in-element delay-3">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required placeholder="Ex: Idriss">
                @error('prenom') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group fade-in-element delay-4">
                <label for="email">Adresse Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="exemple@email.com">
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group fade-in-element delay-5">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="{{ old('telephone') }}" placeholder="+237 6XX XX XX XX">
                @error('telephone') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group fade-in-element delay-6">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="8 caractères minimum">
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group fade-in-element delay-7">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Retapez votre mot de passe">
            </div>

            <button type="submit" class="btn btn-primary btn-block fade-in-element delay-8">M'inscrire</button>
        </form>

        <div class="auth-footer fade-in-element delay-9">
            <p>Déjà un compte ? <a href="{{ route('login') }}">Connectez-vous ici</a>.</p>
        </div>
    </div>
</div>
@endsection