@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title">Nouveau mot de passe</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $erreur)<p>{{ $erreur }}</p>@endforeach
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="form-group">
                <label for="email">Adresse Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" required>
            </div>
            <div class="form-group">
                <label for="password">Nouveau mot de passe</label>
                <input type="password" id="password" name="password" required placeholder="8 caractères minimum, lettres + chiffres">
            </div>
            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Réinitialiser</button>
        </form>
    </div>
</div>
@endsection