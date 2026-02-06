@extends('layouts.app')

@section('title', 'Nouvel Utilisateur')
@section('page-title', 'Créer un Nouvel Utilisateur')

@section('content')
    <div class="container-sm">
        <div class="card">
            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="name" class="form-label">Nom complet</label>
                    <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}"
                        required autofocus>
                    @error('name')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                        required>
                    @error('email')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Mot de passe</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                    @error('password')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-help">Minimum 8 caractères</span>
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                        required>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_admin" id="is_admin" {{ old('is_admin') ? 'checked' : '' }}>
                        <span style="color: var(--gray-300);">Administrateur</span>
                    </label>
                    <span class="form-help">Les administrateurs peuvent gérer les utilisateurs</span>
                </div>

                <div class="flex gap-3 justify-end">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Créer l'utilisateur
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
