@extends('layouts.app')

@section('title', 'Mot de passe oublié - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title">Mot de passe oublié</h2>
        <p class="auth-subtitle">Recevez un lien pour réinitialiser votre mot de passe.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $erreur)<p>{{ $erreur }}</p>@endforeach
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Envoyer le lien</button>
        </form>

        <div class="auth-footer">
            <p><a href="{{ route('login') }}">Retour à la connexion</a></p>
        </div>
    </div>
</div>
@endsection