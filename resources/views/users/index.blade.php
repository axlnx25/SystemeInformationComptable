@extends('layouts.app')

@section('title', 'Gestion des Utilisateurs')
@section('page-title', 'Gestion des Utilisateurs')

@section('content')
    <div class="flex justify-between items-center mb-5">
        <p style="color: var(--gray-400); margin: 0;">
            Gérez les comptes utilisateurs de l'application
        </p>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            + Nouvel Utilisateur
        </a>
    </div>

    <div class="card">
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Journaux</th>
                        <th>Créé le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if ($user->is_admin)
                                    <span class="badge badge-info">👑 Admin</span>
                                @else
                                    <span class="badge badge-success">👤 Utilisateur</span>
                                @endif
                            </td>
                            <td>{{ $user->journals_count }}</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="flex gap-2">
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-secondary btn-sm">
                                        ✏️ Modifier
                                    </a>
                                    @if ($user->id !== auth()->id())
                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                            data-debug-delete="user-index"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm"
                                                data-debug-delete-btn="user-index"
                                                data-confirm-delete="Êtes-vous sûr de vouloir supprimer l'utilisateur {{ $user->name }} ?">
                                                🗑️ Supprimer
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
