@extends('layouts.app')

@section('title', 'Administration - SENTINEL IA')

@section('content')
<div class="container fade-in">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="page-title" style="color: #c084fc;">Gestion des utilisateurs</h1>
        <a href="{{ route('admin.utilisateurs.create') }}" class="btn btn-primary">+ Ajouter un utilisateur</a>
    </div>

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
                            @if ($user->id !== Auth::id())
                                <form action="{{ route('admin.utilisateurs.role', $user) }}" method="POST" style="display:inline-flex; gap: 6px;">
                                    @csrf @method('PATCH')
                                    <select name="role" style="background: var(--bg-surface); color: var(--text-primary); border: 1px solid var(--border-glow); border-radius: 4px; padding: 2px 6px; font-size: 0.85rem;">
                                        @foreach (\App\Enums\UserRole::cases() as $role)
                                            <option value="{{ $role->value }}" @selected($user->role === $role)>{{ ucfirst($role->value) }}</option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn" style="padding: 2px 8px; font-size: 0.8rem; min-height: auto;">OK</button>
                                </form>
                            @else
                                <span style="background: rgba(255,255,255,0.1); padding: 4px 8px; border-radius: 4px; font-size: 0.85rem;">{{ ucfirst($user->role->value ?? 'Utilisateur') }}</span>
                            @endif
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <span style="color: {{ $user->statut === 'actif' ? 'var(--ink-soft)' : 'var(--coral)' }}; font-weight: bold;">
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
                                <form action="{{ route('admin.utilisateurs.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Supprimer cet utilisateur ? Ses signalements existants seront conservés.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn" style="background: transparent; border: 1px solid var(--danger); color: var(--danger); padding: 4px 10px; font-size: 0.85rem; min-height: auto;">Supprimer</button>
                                </form>
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