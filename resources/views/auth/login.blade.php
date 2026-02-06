@extends('layouts.guest')

@section('title', 'Connexion')
@section('auth-title', 'Connexion')
@section('auth-subtitle', 'Accédez à votre espace comptable')

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}"
                placeholder="votre@email.com" required autofocus>
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
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                <input type="checkbox" name="remember" id="remember">
                <span style="color: var(--gray-300); font-size: 0.875rem;">Se souvenir de moi</span>
            </label>
        </div>

        <button type="submit" class="btn btn-primary" style="width: 100%;">
            Se connecter
        </button>

        <div class="text-center mt-4">
            <p style="color: var(--gray-400); margin: 0;">
                Pas encore de compte ?
                <a href="{{ route('register') }}"
                    style="color: var(--primary-400); text-decoration: none; font-weight: 600;">
                    Créer un compte
                </a>
            </p>
        </div>
    </form>
@endsection
