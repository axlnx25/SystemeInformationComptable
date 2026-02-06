@extends('layouts.app')

@section('title', 'Historique - ' . $journal->designation)
@section('page-title', 'Historique - ' . $journal->designation)

@section('content')
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('journals.index') }}" class="btn btn-secondary btn-sm">
            ← Retour aux journaux
        </a>
        <a href="{{ route('journals.operations', $journal) }}" class="btn btn-primary">
            + Nouvelle opération
        </a>
    </div>

    <!-- Journal Totals -->
    <div class="card mb-4">
        <div class="card-header">
            <h3 class="card-title">Totaux du Journal</h3>
        </div>
        <div class="totals-display">
            <div class="total-item">
                <div class="total-label">Total Débit</div>
                <div class="total-value debit">{{ number_format($totals['total_debit'], 2, ',', ' ') }}</div>
            </div>
            <div class="total-item">
                <div class="total-label">Total Crédit</div>
                <div class="total-value credit">{{ number_format($totals['total_credit'], 2, ',', ' ') }}</div>
            </div>
            <div class="total-item">
                <div class="total-label">État</div>
                <div class="total-value">
                    @if ($journal->isBalanced())
                        <span class="badge badge-success">✓ Équilibré</span>
                    @else
                        <span class="badge badge-error">✗ Déséquilibré</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="text-center mt-3">
            <p style="color: var(--gray-400); margin: 0;">
                {{ $totals['operations_count'] }} ligne(s) • {{ $totals['unique_operations'] }} opération(s)
            </p>
        </div>
    </div>

    <!-- Operations List -->
    @if ($groupedOperations->isEmpty())
        <div class="card text-center" style="padding: 4rem;">
            <div style="font-size: 5rem; opacity: 0.3; margin-bottom: 1.5rem;">📊</div>
            <h3>Aucune opération enregistrée</h3>
            <p style="color: var(--gray-400);">Commencez par saisir votre première opération comptable.</p>
            <a href="{{ route('journals.operations', $journal) }}" class="btn btn-primary mt-3">
                Saisir une opération
            </a>
        </div>
    @else
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Opérations Enregistrées</h3>
            </div>

            @foreach ($groupedOperations as $numeroOperation => $operations)
                <div class="card mb-3" style="background: var(--bg-tertiary); border: 1px solid var(--border-color);">
                    <div class="flex justify-between items-center mb-3">
                        <h4 style="margin: 0;">
                            Opération N° {{ $numeroOperation }}
                            <span style="color: var(--gray-400); font-size: 0.875rem; font-weight: normal;">
                                • {{ $operations->first()->date->format('d/m/Y') }}
                                @if ($operations->first()->reference)
                                    • Réf: {{ $operations->first()->reference }}
                                @endif
                            </span>
                        </h4>
                        <form action="{{ route('operations.destroy', [$journal, $numeroOperation]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                data-confirm-delete="Êtes-vous sûr de vouloir supprimer l'opération N°{{ $numeroOperation }} ({{ $operations->count() }} lignes) ?">
                                🗑️ Supprimer
                            </button>
                        </form>
                    </div>

                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>N° Compte</th>
                                    <th>Libellé</th>
                                    <th style="text-align: right;">Débit</th>
                                    <th style="text-align: right;">Crédit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $opDebit = 0;
                                    $opCredit = 0;
                                @endphp
                                @foreach ($operations as $operation)
                                    @php
                                        $opDebit += $operation->debit ?? 0;
                                        $opCredit += $operation->credit ?? 0;
                                    @endphp
                                    <tr>
                                        <td>{{ $operation->date->format('d/m/Y') }}</td>
                                        <td><strong>{{ $operation->numero_compte_general }}</strong></td>
                                        <td>{{ $operation->libelle }}</td>
                                        <td style="text-align: right; color: var(--error); font-family: var(--font-mono);">
                                            @if ($operation->debit)
                                                {{ number_format($operation->debit, 2, ',', ' ') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td
                                            style="text-align: right; color: var(--success); font-family: var(--font-mono);">
                                            @if ($operation->credit)
                                                {{ number_format($operation->credit, 2, ',', ' ') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="background: rgba(14, 165, 233, 0.1); font-weight: 600;">
                                    <td colspan="3" style="text-align: right;">TOTAUX :</td>
                                    <td style="text-align: right; color: var(--error); font-family: var(--font-mono);">
                                        {{ number_format($opDebit, 2, ',', ' ') }}
                                    </td>
                                    <td style="text-align: right; color: var(--success); font-family: var(--font-mono);">
                                        {{ number_format($opCredit, 2, ',', ' ') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-2">
                        @if (abs($opDebit - $opCredit) < 0.01)
                            <span class="badge badge-success">✓ Opération équilibrée</span>
                        @else
                            <span class="badge badge-error">✗ Opération déséquilibrée (Diff:
                                {{ number_format(abs($opDebit - $opCredit), 2, ',', ' ') }})</span>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endsection
