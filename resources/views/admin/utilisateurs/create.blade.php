@extends('layouts.app')

@section('title', 'Ajouter un utilisateur - SENTINEL IA')

@section('content')
<div class="auth-container fade-in">
    <div class="auth-card">
        <h2 class="auth-title" style="color: #c084fc;">Ajouter un utilisateur</h2>

        <form action="{{ route('admin.utilisateurs.store') }}" method="POST" class="auth-form">
            @csrf

            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="{{ old('nom') }}" required>
                @error('nom') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="{{ old('prenom') }}" required>
                @error('prenom') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="telephone">Téléphone</label>
                <input type="text" id="telephone" name="telephone" value="{{ old('telephone') }}">
            </div>

            <div class="form-group">
                <label for="role">Rôle</label>
                <select id="role" name="role" required>
                    @foreach (\App\Enums\UserRole::cases() as $role)
                        <option value="{{ $role->value }}" @selected(old('role') === $role->value)>{{ ucfirst($role->value) }}</option>
                    @endforeach
                </select>
                @error('role') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password">Mot de passe initial</label>
                <input type="password" id="password" name="password" required placeholder="8 caractères minimum, lettres + chiffres">
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-primary btn-block">Créer le compte</button>
        </form>
    </div>
</div>
@endsection