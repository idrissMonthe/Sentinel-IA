@extends('layouts.app')

@section('title', 'Mot de passe oublié - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card auth-card-centered">
        <span class="auth-kicker">Accès sécurisé</span>
        <h1 class="auth-title">Mot de passe oublié ?</h1>
        <p class="auth-subtitle">Saisissez votre adresse email et nous vous enverrons un lien pour choisir un nouveau mot de passe.</p>

        @if (session('status'))
            <div class="alert-success">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Adresse email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="exemple@email.com">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Envoyer le lien</button>
        </form>

        <div class="auth-footer"><a href="{{ route('login') }}">← Retour à la connexion</a></div>
    </div>
</div>
@endsection
