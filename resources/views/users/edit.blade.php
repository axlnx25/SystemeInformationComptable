@extends('layouts.app')

@section('title', 'Modifier Utilisateur')
@section('page-title', 'Modifier l\'Utilisateur')

@section('content')
    <div class="container-sm">
        <div class="card">
            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="name" class="form-label">Nom complet</label>
                    <input type="text" id="name" name="name" class="form-control"
                        value="{{ old('name', $user->name) }}" required autofocus>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control"
                        value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Nouveau mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control">
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-help">Laissez vide pour conserver le mot de passe actuel</span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmer le nouveau mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_admin" id="is_admin"
                            {{ old('is_admin', $user->is_admin) ? 'checked' : '' }}>
                        <span style="color: var(--gray-300);">Administrateur</span>
                    </label>
                    <span class="form-help">Les administrateurs peuvent gérer les utilisateurs</span>
                </div>

                <div class="flex gap-3 justify-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
