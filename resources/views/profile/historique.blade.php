@extends('layouts.app')

@section('title', 'Mon Historique - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <!-- En-tête du profil -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 40px; border-bottom: 1px solid var(--border-glow); padding-bottom: 20px;">
        <div>
            <h1 class="page-title" style="color: var(--blue-glow); margin-bottom: 5px;">Mon Espace</h1>
            <p class="text-secondary">Gérez vos informations et suivez l'impact de vos signalements.</p>
        </div>
        <a href="{{ route('signalements.create') }}" class="btn btn-signal" style="box-shadow: 0 0 15px var(--danger)40;">+ Nouveau Signalement</a>
    </div>

    <div style="display: flex; gap: 30px;">

<aside style="width: 250px; flex-shrink: 0;">
    <div class="card" style="padding: 20px;">
        <nav style="display: flex; flex-direction: column; gap: 10px;">
            <a href="{{ route('profile.edit') }}" style="color: var(--text-secondary); text-decoration: none; padding: 10px; border-radius: 8px; transition: 0.3s;">
                👤 Mes informations & Sécurité
            </a>
            <a href="{{ route('profile.historique') }}" style="background: rgba(0, 212, 255, 0.1); color: var(--blue-glow); text-decoration: none; padding: 10px; border-radius: 8px; font-weight: bold; border-left: 3px solid var(--blue-glow);">
                📜 Historique des signalements
            </a>
        </nav>
    </div>
</aside>

        <!-- Contenu principal : Historique -->
        <div style="flex: 1;">
            <!-- Filtres rapides -->
            <div style="margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap;">
                <a href="{{ route('profile.historique') }}" class="badge {{ request('statut') ? 'badge-outline' : 'badge-active' }}">Tous</a>
                <a href="{{ route('profile.historique', ['statut' => 'en_attente']) }}" class="badge {{ request('statut') == 'en_attente' ? 'badge-warning' : 'badge-outline' }}">En attente</a>
                <a href="{{ route('profile.historique', ['statut' => 'valide']) }}" class="badge {{ request('statut') == 'valide' ? 'badge-success' : 'badge-outline' }}">Validés</a>
                <a href="{{ route('profile.historique', ['statut' => 'rejete']) }}" class="badge {{ request('statut') == 'rejete' ? 'badge-danger' : 'badge-outline' }}">Rejetés</a>
            </div>

            <!-- Liste des signalements -->
            <div style="display: flex; flex-direction: column; gap: 15px;">
                @forelse ($signalements as $signalement)
                    <div class="card fade-in-element" style="display: flex; justify-content: space-between; align-items: center; padding: 20px;">
                        
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                <span style="font-weight: bold; font-size: 1.1rem; color: var(--text-primary);">
                                    {{ ucfirst($signalement->type_entite) }} : {{ $signalement->valeur_entite }}
                                </span>
                                
                                @if($signalement->statut === 'en_attente')
                                    <span style="background: rgba(255, 193, 7, 0.1); color: var(--warning); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; border: 1px solid var(--warning);">En attente</span>
                                @elseif($signalement->statut === 'valide')
                                    <span style="background: rgba(40, 167, 69, 0.1); color: var(--success); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; border: 1px solid var(--success);">Validé</span>
                                @else
                                    <span style="background: rgba(220, 53, 69, 0.1); color: var(--danger); padding: 4px 10px; border-radius: 20px; font-size: 0.8rem; border: 1px solid var(--danger);">Rejeté</span>
                                @endif
                            </div>
                            
                            <p class="text-secondary" style="font-size: 0.9rem; margin-bottom: 5px;">
                                Soumis le {{ $signalement->created_at->format('d/m/Y à H:i') }}
                            </p>
                            <p style="color: var(--text-secondary); font-size: 0.95rem;">
                                {{ Str::limit($signalement->description, 80) }}
                            </p>
                        </div>

                        <div style="margin-left: 20px; text-align: right;">
                            <a href="{{ route('signalements.show', $signalement->id) }}" class="btn btn-secondary" style="padding: 8px 15px; font-size: 0.9rem;">Détails</a>
                        </div>
                    </div>
                @empty
                    <div class="card text-center" style="padding: 40px; border: 1px dashed var(--border-glow); background: transparent;">
                        <div style="font-size: 2.5rem; margin-bottom: 15px;">🕵️‍♂️</div>
                        <p style="font-size: 1.1rem; color: var(--text-secondary);">Votre historique est vierge.</p>
                        <p style="font-size: 0.9rem; color: var(--text-secondary); margin-bottom: 20px;">Aidez la communauté en signalant votre première menace.</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div style="margin-top: 30px; display: flex; justify-content: center;">
                {{ $signalements->links() }}
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