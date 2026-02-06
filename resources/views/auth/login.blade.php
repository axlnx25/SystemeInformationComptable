@extends('layouts.guest')

@section('title', 'Connexion')
@section('auth-title', 'Bienvenue !')
@section('auth-subtitle', 'Connectez-vous à votre espace comptable')

@section('content')
    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="formGroup">
            <label for="email" class="formLabel">Adresse email</label>
            <div class="inputGroup">
                <div class="inputIcon">
                    <span class="material-symbols-outlined">mail</span>
                </div>
                <input type="email" id="email" name="email" class="formInput" value="{{ old('email') }}"
                    placeholder="votre@email.com" required autofocus>
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
        </div>

        <div class="formGroup">
            <div class="checkboxGroup">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Se souvenir de moi</label>
            </div>
        </div>

        <button type="submit" class="btnPrimary">
            <span class="material-symbols-outlined" style="font-size: 20px;">login</span>
            Se connecter
        </button>

        <div class="authFooter">
            <p>
                Pas encore de compte ?
                <a href="{{ route('register') }}">Créer un compte</a>
            </p>
        </div>
    </form>
@endsection
