@extends('layouts.app')

@section('title', 'Mon Historique - SENTINEL IA')

@section('content')
<div class="profile-history-page fade-in">
    <!-- En-tête du profil -->
    <div class="profile-history-header">
        <div>
            <h1 class="page-title" style="color: var(--blue-glow); margin-bottom: 5px;">Mon Espace</h1>
            <p class="text-secondary">Gérez vos informations et suivez l'impact de vos signalements et analyses.</p>
        </div>
        <a href="{{ route('signalements.create') }}" class="btn btn-signal">+ Nouveau Signalement</a>
    </div>

    <div class="profile-history-layout">
        <!-- Barre latérale -->
        <aside class="profile-history-sidebar">
            <div class="card profile-history-nav">
                <nav>
                    <a href="{{ route('profile.edit') }}" style="color: var(--text-secondary); text-decoration: none; padding: 10px; border-radius: 8px; transition: 0.3s;">
                        Mes informations & Sécurité
                    </a>
                    <a href="{{ route('profile.historique') }}" style="background: rgba(0, 212, 255, 0.1); color: var(--blue-glow); text-decoration: none; padding: 10px; border-radius: 8px; font-weight: bold; border-left: 3px solid var(--blue-glow);">
                        Historique des activités
                    </a>
                </nav>
            </div>
        </aside>

        <!-- Contenu principal : Historique -->
        <div class="profile-history-content">
            <!-- Filtres rapides (S'appliquent principalement aux signalements) -->
            <div class="profile-history-filters">
                <a href="{{ route('profile.historique') }}" class="badge {{ request('statut') ? 'badge-outline' : 'badge-active' }}">Tous</a>
                <a href="{{ route('profile.historique', ['statut' => 'en_attente']) }}" class="badge {{ request('statut') == 'en_attente' ? 'badge-warning' : 'badge-outline' }}">En attente</a>
                <a href="{{ route('profile.historique', ['statut' => 'valide']) }}" class="badge {{ request('statut') == 'valide' ? 'badge-success' : 'badge-outline' }}">Validés</a>
                <a href="{{ route('profile.historique', ['statut' => 'rejete']) }}" class="badge {{ request('statut') == 'rejete' ? 'badge-danger' : 'badge-outline' }}">Rejetés</a>
            </div>

            <!-- Liste des activités (Signalements + Analyses) -->
            <div class="profile-history-list">
                @forelse ($activites as $activite)
                    <div class="card profile-history-item fade-in-element">
                        
                        <div class="profile-history-item-body">
                            <div class="profile-history-item-meta">
                                
                                <!-- Badge de Type (Signalement ou Analyse) -->
                                @if($activite['type'] === 'signalement')
                                    <span style="background: rgba(0, 212, 255, 0.1); color: var(--blue-glow); padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; border: 1px solid var(--blue-glow); text-transform: uppercase; font-weight: bold;">
                                        Signalement
                                    </span>
                                @else
                                    <span style="background: rgba(177, 156, 217, 0.1); color: #b19cd9; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; border: 1px solid #b19cd9; text-transform: uppercase; font-weight: bold;">
                                        Analyse IA
                                    </span>
                                @endif

                                <!-- Label de l'activité -->
                                <span style="font-weight: bold; font-size: 1.1rem; color: var(--text-primary);">
                                    {{ $activite['label'] }}
                                </span>
                                
                                <!-- Badge de Statut (Uniquement si existant) -->
                                @if($activite['statut'])
                                    @if($activite['statut'] === 'En attente')
                                        <span style="background: rgba(255, 193, 7, 0.1); color: var(--warning); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; border: 1px solid var(--warning);">En attente</span>
                                    @elseif($activite['statut'] === 'Validé')
                                        <span style="background: rgba(40, 167, 69, 0.1); color: var(--success); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; border: 1px solid var(--success);">Validé</span>
                                    @else
                                        <span style="background: rgba(220, 53, 69, 0.1); color: var(--danger); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; border: 1px solid var(--danger);">Rejeté</span>
                                    @endif
                                @endif
                            </div>
                            
                            <p class="text-secondary" style="font-size: 0.9rem; margin-bottom: 0;">
                                Enregistré le {{ $activite['date']->format('d/m/Y à H:i') }}
                            </p>
                        </div>

                        <div class="profile-history-item-action">
                            <a href="{{ $activite['lien'] }}" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.9rem;">Détails</a>
                        </div>
                    </div>
                @empty
                    <div class="card profile-history-empty text-center">
                        <p style="font-size: 1.1rem; color: var(--text-secondary); margin-bottom: 5px;">Votre historique est vierge.</p>
                        <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 0;">Analysez ou signalez votre première menace pour aider la communauté.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .badge { padding: 6px 14px; border-radius: 20px; text-decoration: none; font-size: 0.85rem; transition: all 0.3s ease; }
    .badge-outline { border: 1px solid var(--border-glow); color: var(--text-secondary); }
    .badge-outline:hover { background: rgba(255,255,255,0.05); color: var(--text-primary); }
    .badge-active { background: var(--bg-surface); border: 1px solid var(--text-primary); color: var(--text-primary); }
    .badge-warning { background: rgba(255, 193, 7, 0.1); border: 1px solid var(--warning); color: var(--warning); }
    .badge-success { background: rgba(40, 167, 69, 0.1); border: 1px solid var(--success); color: var(--success); }
    .badge-danger { background: rgba(220, 53, 69, 0.1); border: 1px solid var(--danger); color: var(--danger); }
</style>
@endsection
