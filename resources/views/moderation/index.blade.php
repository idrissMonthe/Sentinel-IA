@extends('layouts.app')

@section('title', 'Modération - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 class="page-title">File d'attente de Modération</h1>
        <a href="{{ route('moderation.doublons') }}" class="btn btn-secondary" style="border: 1px solid var(--warning); color: var(--warning);">Voir les doublons suspects</a>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-glow);">
            <thead style="background: var(--bg-surface);">
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
                        <td style="padding: 15px;">{{ $signalement->created_at->format('d/m/Y H:i') }}</td>
                        <td style="padding: 15px; font-weight: bold;">{{ $signalement->entiteSuspecte->valeur }}</td>
                        <td style="padding: 15px; max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $signalement->description }}</td>
                        <td style="padding: 15px; text-align: right; display: flex; justify-content: flex-end; gap: 10px;">
                            <form action="{{ route('moderation.valider', $signalement) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn" style="background: var(--success); color: #000; padding: 6px 12px; min-height: auto;">Valider</button>
                            </form>
                            <form action="{{ route('moderation.rejeter', $signalement) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn btn-signal" style="padding: 6px 12px; min-height: auto;">Rejeter</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 30px; text-align: center;">La file d'attente est vide. Beau travail !</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $signalements->links() }}
    </div>
</div>
@endsection