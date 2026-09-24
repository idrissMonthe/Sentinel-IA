@extends('layouts.app')

@section('title', 'Vérification - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title">Vérification en deux étapes</h2>
        <p class="auth-subtitle">Un code à 6 chiffres a été envoyé à votre adresse email. Il expire dans 10 minutes.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $erreur)
                    <p>{{ $erreur }}</p>
                @endforeach
            </div>
        @endif

        @if (session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <form action="{{ route('verification.code.verifier') }}" method="POST" class="auth-form">
            @csrf
            <div class="form-group">
                <label for="code">Code de vérification</label>
                <input type="text" id="code" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" required autofocus placeholder="123456">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Vérifier</button>
        </form>

        <form action="{{ route('verification.code.renvoyer') }}" method="POST" style="margin-top: 15px; text-align:center;">
            @csrf
            <button type="submit" class="btn" style="background:transparent; color: var(--blue-shield); text-decoration: underline; border:none;">Renvoyer le code</button>
        </form>
    </div>
</div>
@endsection