@extends('layouts.app')

@section('title', 'Tableau de bord')
@section('page-title', 'Tableau de bord')

@section('content')
    <div class="grid grid-cols-3 gap-4 mb-5">
        <!-- Stat Card 1 -->
        <div class="card">
            <div class="flex justify-between items-center">
                <div>
                    <p style="color: var(--gray-400); font-size: 0.875rem; margin: 0;">Total Journaux</p>
                    <h3 style="font-size: 2rem; margin: 0.5rem 0 0 0;">{{ $totalJournals }}</h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">📘</div>
            </div>
        </div>

        <!-- Stat Card 2 -->
        <div class="card">
            <div class="flex justify-between items-center">
                <div>
                    <p style="color: var(--gray-400); font-size: 0.875rem; margin: 0;">Total Opérations</p>
                    <h3 style="font-size: 2rem; margin: 0.5rem 0 0 0;">{{ $totalOperations }}</h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">📊</div>
            </div>
        </div>

        <!-- Stat Card 3 -->
        <div class="card">
            <div class="flex justify-between items-center">
                <div>
                    <p style="color: var(--gray-400); font-size: 0.875rem; margin: 0;">Utilisateur</p>
                    <h3 style="font-size: 1.25rem; margin: 0.5rem 0 0 0;">{{ auth()->user()->name }}</h3>
                </div>
                <div style="font-size: 3rem; opacity: 0.3;">👤</div>
            </div>
        </div>
    </div>

    <!-- Recent Journals -->
    <div class="card">
        <div class="card-header">
            <div class="flex justify-between items-center">
                <h3 class="card-title">Journaux Récents</h3>
                <a href="{{ route('journals.create') }}" class="btn btn-primary btn-sm">
                    + Nouveau Journal
                </a>
            </div>
        </div>

        @if ($recentJournals->isEmpty())
            <div class="text-center" style="padding: 3rem;">
                <div style="font-size: 4rem; opacity: 0.3; margin-bottom: 1rem;">📘</div>
                <p style="color: var(--gray-400); margin: 0;">Aucun journal pour le moment.</p>
                <a href="{{ route('journals.create') }}" class="btn btn-primary mt-3">
                    Créer votre premier journal
                </a>
            </div>
        @else
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Désignation</th>
                            <th>Opérations</th>
                            <th>Total Débit</th>
                            <th>Total Crédit</th>
                            <th>État</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($recentJournals as $journal)
                            <tr>
                                <td><strong>{{ $journal->designation }}</strong></td>
                                <td>{{ $journal->operations_count }}</td>
                                <td style="color: var(--error); font-family: var(--font-mono);">
                                    {{ number_format($journal->total_debit, 2, ',', ' ') }}
                                </td>
                                <td style="color: var(--success); font-family: var(--font-mono);">
                                    {{ number_format($journal->total_credit, 2, ',', ' ') }}
                                </td>
                                <td>
                                    @if ($journal->is_balanced)
                                        <span class="badge badge-success">✓ Équilibré</span>
                                    @else
                                        <span class="badge badge-warning">⚠ Déséquilibré</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <a href="{{ route('journals.operations', $journal) }}"
                                            class="btn btn-primary btn-sm">
                                            Saisir
                                        </a>
                                        <a href="{{ route('journals.history', $journal) }}"
                                            class="btn btn-secondary btn-sm">
                                            Historique
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($totalJournals > 5)
                <div class="text-center mt-4">
                    <a href="{{ route('journals.index') }}" class="btn btn-secondary">
                        Voir tous les journaux
                    </a>
                </div>
            @endif
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 gap-4 mt-5">
        <div class="card" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(217, 70, 239, 0.1));">
            <h4 style="margin-bottom: 1rem;">🚀 Actions Rapides</h4>
            <div class="flex flex-col gap-2">
                <a href="{{ route('journals.create') }}" class="btn btn-primary">
                    + Créer un nouveau journal
                </a>
                <a href="{{ route('journals.index') }}" class="btn btn-secondary">
                    📋 Voir tous mes journaux
                </a>
            </div>
        </div>

        <div class="card" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(245, 158, 11, 0.1));">
            <h4 style="margin-bottom: 1rem;">📚 Guide Rapide</h4>
            <ul style="color: var(--gray-300); line-height: 1.8; margin: 0; padding-left: 1.5rem;">
                <li>Créez un journal pour organiser vos opérations</li>
                <li>Saisissez des opérations en partie double</li>
                <li>Consultez l'historique et les totaux</li>
                <li>Assurez-vous que tout est équilibré</li>
            </ul>
        </div>
    </div>
@endsection
