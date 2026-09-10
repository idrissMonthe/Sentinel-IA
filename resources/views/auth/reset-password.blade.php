@extends('layouts.app')

@section('title', 'Nouveau mot de passe - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card auth-card-centered">
        <span class="auth-kicker">Accès sécurisé</span>
        <h1 class="auth-title">Créer un nouveau mot de passe</h1>
        <p class="auth-subtitle">Choisissez un mot de passe d’au moins 8 caractères pour sécuriser votre compte.</p>

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required autofocus placeholder="exemple@email.com">
            </div>
            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <div class="password-input"><input type="password" id="password" name="password" required placeholder="8 caractères minimum"><button type="button" class="password-toggle" aria-label="Afficher le mot de passe" data-password-toggle="password">Afficher</button></div>
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <div class="password-input"><input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Retapez la confirmation du mot de passe"><button type="button" class="password-toggle" aria-label="Afficher la confirmation du mot de passe" data-password-toggle="password_confirmation">Afficher</button></div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Réinitialiser le mot de passe</button>
        </form>
    </div>
</div>
@endsection
