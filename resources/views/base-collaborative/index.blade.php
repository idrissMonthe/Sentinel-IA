@extends('layouts.app')

@section('title', 'Base Collaborative - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <div class="hero-section" style="padding: 40px 20px;">
        <h1>Rechercher une arnaque</h1>
        <p>Vérifiez si un numéro, un email ou un lien a déjà été signalé par la communauté.</p>
        
        <form action="{{ route('base-collaborative.index') }}" method="GET" class="search-form" style="max-width: 600px; margin: 0 auto; display: flex; gap: 10px;">
            <select name="type" class="form-control" style="width: 30%;">
                <option value="">Tous les types</option>
                <option value="numero" {{ request('type') == 'numero' ? 'selected' : '' }}>Numéro</option>
                <option value="email" {{ request('type') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="lien" {{ request('type') == 'lien' ? 'selected' : '' }}>Lien</option>
            </select>
            <input type="text" name="valeur" class="form-control" placeholder="Ex: +237 6XX XX XX XX, url suspecte..." value="{{ request('valeur') }}" required style="flex: 1;">
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>

    <div class="features-grid" style="margin-top: 30px;">
        @forelse ($resultats as $entite)
            <div class="card fade-in-element">
                <div style="display: flex; justify-content: space-between; align-items: start;">
                    <h3>{{ ucfirst($entite->type) }} suspect</h3>
                    <span class="score-circle danger" style="width: 40px; height: 40px; border-width: 2px; font-size: 1rem; margin: 0;">
                        {{ $entite->nombre_signalement }}
                    </span>
                </div>
                <p style="font-size: 1.1rem; color: var(--text-primary); margin: 10px 0; word-break: break-all;">
                    {{ $entite->valeur }}
                </p>
                <a href="{{ route('base-collaborative.show', $entite) }}" class="btn btn-secondary" style="width: 100%; text-align: center; border: 1px solid var(--border-glow); margin-top: 15px;">Voir les détails</a>
            </div>
        @empty
            <div class="card" style="grid-column: 1 / -1; text-align: center;">
                <p>Aucun résultat trouvé pour cette recherche. Soyez tout de même prudent.</p>
            </div>
        @endforelse
    </div>

    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $resultats->links() }}
    </div>
</div>
@endsection