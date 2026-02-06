@extends('layouts.guest')

@section('title', 'Inscription')
@section('auth-title', 'Créer un compte')
@section('auth-subtitle', 'Commencez à gérer votre comptabilité')

@section('content')
    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="name" class="form-label">Nom complet</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}"
                placeholder="Jean Dupont" required autofocus>
            @error('name')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                placeholder="votre@email.com" required>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Mot de passe</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
            <span class="form-help">Minimum 8 caractères</span>
        </div>

        <div class="form-group">
            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                placeholder="••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            Créer mon compte
        </button>

        <div class="text-center mt-4">
            <p style="color: var(--gray-400); margin: 0;">
                Déjà un compte ?
                <a href="{{ route('login') }}" style="color: var(--primary-400); text-decoration: none; font-weight: 600;">
                    Se connecter
                </a>
            </p>
        </div>
    </form>
@endsection
