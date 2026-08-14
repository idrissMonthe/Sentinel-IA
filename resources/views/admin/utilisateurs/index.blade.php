@extends('layouts.app')

@section('title', 'Administration - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <h1 class="page-title" style="color: #c084fc;">Gestion des utilisateurs</h1>
    
    <div style="overflow-x: auto; margin-top: 20px;">
        <table style="width: 100%; border-collapse: collapse; background: var(--bg-card); border-radius: 8px; overflow: hidden; border: 1px solid var(--border-glow);">
            <thead style="background: var(--bg-surface);">
                <tr>
                    <th style="padding: 15px; text-align: left; color: var(--text-secondary);">Utilisateur</th>
                    <th style="padding: 15px; text-align: left; color: var(--text-secondary);">Email</th>
                    <th style="padding: 15px; text-align: center; color: var(--text-secondary);">Rôle</th>
                    <th style="padding: 15px; text-align: center; color: var(--text-secondary);">Statut</th>
                    <th style="padding: 15px; text-align: right; color: var(--text-secondary);">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($utilisateurs as $user)
                    <tr style="border-top: 1px solid var(--border-glow);">
                        <td style="padding: 15px; font-weight: bold;">{{ $user->nom }} {{ $user->prenom }}</td>
                        <td style="padding: 15px; color: var(--text-secondary);">{{ $user->email }}</td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">{{ ucfirst($user->role->value ?? 'Utilisateur') }}</span>
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="color: {{ $user->statut === 'actif' ? 'var(--success)' : 'var(--danger)' }}; font-weight: bold;">
                                {{ ucfirst($user->statut) }}
                            </span>
                        </td>
                        <td style="padding: 15px; text-align: right;">
                            @if ($user->id !== Auth::id())
                                @if ($user->statut === 'actif')
                                    <form action="{{ route('admin.utilisateurs.bloquer', $user) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn btn-signal" style="padding: 4px 10px; font-size: 0.85rem; min-height: auto;">Bloquer</button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.utilisateurs.debloquer', $user) }}" method="POST" style="display:inline;">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn" style="background: var(--success); color: #000; padding: 4px 10px; font-size: 0.85rem; min-height: auto;">Débloquer</button>
                                    </form>
                                @endif
                            @else
                                <span class="text-secondary" style="font-size: 0.85rem;">C'est vous</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="margin-top: 30px; display: flex; justify-content: center;">
        {{ $utilisateurs->links() }}
    </div>
</div>
@endsection