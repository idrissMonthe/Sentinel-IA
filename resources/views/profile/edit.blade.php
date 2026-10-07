@extends('layouts.app')

@section('title', 'Éditer mon profil - SENTINEL IA')

@section('content')
<div class="profile-container fade-in">
    <div class="profile-header">
        <h1 class="page-title">Paramètres du compte</h1>
        <p class="text-secondary">Gérez vos informations personnelles et votre sécurité.</p>
    </div>

    <div class="profile-grid">
        <!-- ================= FORMULAIRE 1 : INFORMATIONS PERSONNELLES ================= -->
        <div class="profile-card fade-in-element delay-1">
            <h2>Informations Personnelles</h2>
            
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="nom">Nom</label>
                    <!-- On priorise l'ancienne saisie (old) en cas d'erreur, sinon on affiche la donnée en base -->
                    <input type="text" id="nom" name="nom" value="{{ old('nom', Auth::user()->nom) }}" required>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom" value="{{ old('prenom', Auth::user()->prenom) }}" required>
                </div>

                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <input type="text" id="telephone" name="telephone" value="{{ old('telephone', Auth::user()->telephone) }}" placeholder="+237 6XX XX XX XX">
                </div>

                <!-- L'email n'est pas modifiable dans ton array de validation backend, donc on peut l'afficher en readonly pour info -->
                <div class="form-group">
                    <label for="email">Adresse Email (Non modifiable)</label>
                    <input type="email" id="email" value="{{ Auth::user()->email }}" disabled style="opacity: 0.7; cursor: not-allowed;">
                </div>

                <button type="submit" class="btn btn-primary">Mettre à jour le profil</button>
            </form>
        </div>

        <!-- ================= FORMULAIRE 2 : SÉCURITÉ / MOT DE PASSE ================= -->
        <div class="profile-card fade-in-element delay-2">
            <h2>Sécurité</h2>
            
            <form action="{{ route('profile.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Mot de passe actuel</label>
                    <input type="password" id="current_password" name="current_password" required>
                </div>

                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" required placeholder="8 caractères minimum">
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="btn btn-signal" style="margin-top: 15px;">Modifier le mot de passe</button>
            </form>
        </div>
    </div>

    <section class="danger-zone fade-in-element delay-3" aria-labelledby="suppression-compte-title">
        <div>
            <p class="danger-zone-eyebrow">Zone sensible</p>
            <h2 id="suppression-compte-title">Supprimer mon compte</h2>
            <p>Votre accès sera immédiatement désactivé. Vos signalements déjà publiés pourront être conservés afin de protéger la communauté.</p>
        </div>
        <form action="{{ route('profile.destroy') }}" method="POST" data-confirm="Cette action est définitive. Voulez-vous réellement supprimer votre compte Sentinel IA ?">
            @csrf
            @method('DELETE')
            <div class="form-group">
                <label for="delete_password">Confirmez avec votre mot de passe</label>
                <input type="password" id="delete_password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-danger">Supprimer définitivement mon compte</button>
        </form>
    </section>
</div>
@endsection
