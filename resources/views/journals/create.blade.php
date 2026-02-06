@extends('layouts.app')

@section('title', 'Nouveau Journal')
@section('page-title', 'Créer un Nouveau Journal')

@section('content')
    <div class="container-sm">
        <div class="card">
            <form action="{{ route('journals.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="designation" class="form-label">Désignation du Journal</label>
                    <input type="text" id="designation" name="designation" class="form-control"
                        value="{{ old('designation') }}" placeholder="Ex: Journal des Ventes, Journal de Banque..." required
                        autofocus>
                    @error('designation')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                    <span class="form-help">
                        Donnez un nom descriptif à votre journal (ex: Ventes, Achats, Banque, Caisse...)
                    </span>
                </div>

                <div class="flex gap-3 justify-end">
                    <a href="{{ route('journals.index') }}" class="btn btn-secondary">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Créer le journal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
