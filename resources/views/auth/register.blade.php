@extends('layouts.guest')

@section('title', 'Inscription')
@section('auth-title', 'Créer un compte')
@section('auth-subtitle', 'Commencez à gérer votre comptabilité dès maintenant')

@section('content')
    <form action="{{ route('register') }}" method="POST">
        @csrf

        <div class="formGroup">
            <label for="name" class="formLabel">Nom complet</label>
            <div class="inputGroup">
                <div class="inputIcon">
                    <span class="material-symbols-outlined">person</span>
                </div>
                <input type="text" id="name" name="name" class="formInput" value="{{ old('name') }}"
                    placeholder="Jean Dupont" required autofocus>
            </div>
            @error('name')
                <span class="formError">{{ $message }}</span>
            @enderror
        </div>

        <div class="formGroup">
            <label for="email" class="formLabel">Adresse email</label>
            <div class="inputGroup">
                <div class="inputIcon">
                    <span class="material-symbols-outlined">mail</span>
                </div>
                <input type="email" id="email" name="email" class="formInput" value="{{ old('email') }}"
                    placeholder="votre@email.com" required>
            </div>
            @error('email')
                <span class="formError">{{ $message }}</span>
            @enderror
        </div>

        <div class="formGroup">
            <label for="password" class="formLabel">Mot de passe</label>
            <div class="inputGroup">
                <div class="inputIcon">
                    <span class="material-symbols-outlined">lock</span>
                </div>
                <input type="password" id="password" name="password" class="formInput" placeholder="••••••••" required>
            </div>
            @error('password')
                <span class="formError">{{ $message }}</span>
            @enderror
            <span class="formHelp">
                <span class="material-symbols-outlined" style="font-size: 14px; vertical-align: middle;">info</span>
                Minimum 8 caractères
            </span>
        </div>

        <div class="formGroup">
            <label for="password_confirmation" class="formLabel">Confirmer le mot de passe</label>
            <div class="inputGroup">
                <div class="inputIcon">
                    <span class="material-symbols-outlined">lock_reset</span>
                </div>
                <input type="password" id="password_confirmation" name="password_confirmation" class="formInput"
                    placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" class="btnPrimary">
            <span class="material-symbols-outlined" style="font-size: 20px;">person_add</span>
            Créer mon compte
        </button>

        <div class="authFooter">
            <p>
                Déjà un compte ?
                <a href="{{ route('login') }}">Se connecter</a>
            </p>
        </div>
    </form>
@endsection
