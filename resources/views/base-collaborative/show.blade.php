@extends('layouts.app')

@section('title', 'Détail de l\'entité - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <div class="card" style="border-color: var(--danger); text-align: center; margin-bottom: 30px;">
        <h1 class="page-title" style="color: var(--danger);">{{ ucfirst($entiteSuspecte->type) }} : {{ $entiteSuspecte->valeur }}</h1>
        <p class="text-secondary">Signalé <strong>{{ $entiteSuspecte->nombre_signalement }} fois</strong> par la communauté.</p>
    </div>

    <h2 style="margin-bottom: 20px;">Témoignages validés</h2>
    <div class="features-grid">
        @forelse ($signalements as $signalement)
            <div class="card fade-in-element">
                <p class="text-secondary" style="font-size: 0.85rem; margin-bottom: 10px;">
                    Le {{ $signalement->created_at->format('d/m/Y') }} 
                    @if($signalement->ville) à {{ $signalement->ville }} @endif
                </p>
                <p style="white-space: pre-line;">{{ $signalement->description }}</p>
            </div>
        @empty
            <p>Aucun témoignage détaillé pour le moment.</p>
        @endforelse
    </div>

    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $signalements->links() }}
    </div>
</div>
@endsection