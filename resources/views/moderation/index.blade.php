@extends('layouts.app')

@section('title', 'Modération - SENTINEL IA')

@section('content')
<div class="moderation-page fade-in">
    <div class="moderation-header">
        <h1 class="page-title">File d'attente de Modération</h1>
        <a href="{{ route('moderation.doublons') }}" class="btn btn-secondary" style="border: 1px solid var(--warning); color: var(--warning);">Voir les doublons suspects</a>
    </div>

    <div class="moderation-table-wrap">
        <table class="moderation-table">
            <thead>
                <tr>
                    <th style="padding: 15px; text-align: left; color: var(--text-secondary);">Date</th>
                    <th style="padding: 15px; text-align: left; color: var(--text-secondary);">Entité</th>
                    <th style="padding: 15px; text-align: left; color: var(--text-secondary);">Description</th>
                    <th style="padding: 15px; text-align: right; color: var(--text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($signalements as $signalement)
                    <tr style="border-top: 1px solid var(--border-glow);">
                        <td data-label="Date">{{ $signalement->created_at->format('d/m/Y H:i') }}</td>
                        <td data-label="Entité" class="moderation-entity">{{ $signalement->entiteSuspecte->valeur }}</td>
                        <td data-label="Description" class="moderation-description">{{ $signalement->description }}</td>
                        <td data-label="Actions" class="moderation-actions">
                            <form action="{{ route('moderation.valider', $signalement) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn" style="background: var(--success); color: #000; padding: 6px 12px; min-height: auto;">Valider</button>
                                <a href="{{ route('signalements.show', $signalement) }}" class="btn btn-secondary">Voir le détail</a>
                            </form>
                            <form action="{{ route('moderation.rejeter', $signalement) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-signal" style="padding: 6px 12px; min-height: auto;">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="moderation-empty">La file d'attente est vide. Beau travail !</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $signalements->links() }}
    </div>
</div>
@endsection
