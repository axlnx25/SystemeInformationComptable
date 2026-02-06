@extends('layouts.app')

@section('title', 'Modifier Journal')
@section('page-title', 'Modifier le Journal')

@section('content')
    <div class="container-sm">
        <div class="card">
            <form action="{{ route('journals.update', $journal) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="designation" class="form-label">Désignation du Journal</label>
                    <input type="text" id="designation" name="designation" class="form-control"
                        value="{{ old('designation', $journal->designation) }}" required autofocus>
                    @error('designation')
                        <span class="form-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3 justify-end">
                    <a href="{{ route('journals.index') }}" class="btn btn-secondary">
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
