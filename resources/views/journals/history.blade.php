@extends('layouts.app')

@section('title', 'Historique - ' . $journal->designation)

@section('content')
    <section class="page pageWide">
        <header class="pageHeader">
            <div class="historyTop">
                <div class="journalTitleBar">
                    <a href="{{ route('journals.index') }}" class="link"
                        style="display: inline-flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                        <span class="material-symbols-outlined" style="font-size: 18px;">arrow_back</span>
                        Retour aux journaux
                    </a>
                    <h1 class="pageTitle">{{ $journal->designation }}</h1>
                    <p class="muted">Historique des opérations</p>
                </div>
                <div>
                    <a href="{{ route('journals.operations', $journal) }}" class="btnPrimary"
                        style="display: inline-flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined">add_circle</span>
                        Nouvelle opération
                    </a>
                </div>
            </div>
        </header>

        <!-- KPI Cards for Journal -->
        <div class="dashboardGrid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));">
            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                    <span class="material-symbols-outlined">arrow_upward</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Total Débit</div>
                    <div class="statCardValue mono">{{ number_format($totals['total_debit'], 0, ',', ' ') }}</div>
                    <div class="statCardHint">FCFA</div>
                </div>
            </div>

            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">
                    <span class="material-symbols-outlined">arrow_downward</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Total Crédit</div>
                    <div class="statCardValue mono">{{ number_format($totals['total_credit'], 0, ',', ' ') }}</div>
                    <div class="statCardHint">FCFA</div>
                </div>
            </div>

            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Opérations</div>
                    <div class="statCardValue">{{ $totals['unique_operations'] }}</div>
                    <div class="statCardHint">{{ $totals['operations_count'] }} ligne(s)</div>
                </div>
            </div>

            <div class="statCard">
                <div class="statCardIcon"
                    style="background: {{ $journal->isBalanced() ? 'rgba(34, 197, 94, 0.1)' : 'rgba(245, 158, 11, 0.1)' }}; color: {{ $journal->isBalanced() ? '#16a34a' : '#d97706' }};">
                    <span class="material-symbols-outlined">{{ $journal->isBalanced() ? 'check_circle' : 'warning' }}</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">État</div>
                    <div class="statCardValue" style="font-size: 18px;">
                        @if ($journal->isBalanced())
                            <span class="badgeOk">Équilibré</span>
                        @else
                            <span class="badgeWarn">Déséquilibré</span>
                        @endif
                    </div>
                    <div class="statCardHint">Journal</div>
                </div>
            </div>
        </div>

        <!-- Operations List -->
        @if ($groupedOperations->isEmpty())
            <div class="panel">
                <div class="emptyState">
                    <div class="emptyStateIcon">
                        <span class="material-symbols-outlined">receipt_long</span>
                    </div>
                    <h3 class="emptyStateTitle">Aucune opération enregistrée</h3>
                    <p class="emptyStateDesc">
                        Commencez par saisir votre première opération comptable.
                    </p>
                    <a href="{{ route('journals.operations', $journal) }}" class="btnPrimary"
                        style="display: inline-flex; align-items: center; gap: 8px;">
                        <span class="material-symbols-outlined">add_circle</span>
                        Saisir une opération
                    </a>
                </div>
            </div>
        @else
            @foreach ($groupedOperations as $numeroOperation => $operations)
                <div class="panel operationCard">
                    <div class="operationCardHeader">
                        <div class="operationCardTitle">
                            <span class="material-symbols-outlined" style="color: #2563eb;">receipt</span>
                            <div>
                                <h3 style="margin: 0; font-size: 16px; font-weight: 700;">Opération N°
                                    {{ $numeroOperation }}</h3>
                                <p class="muted" style="margin: 4px 0 0 0; font-size: 13px;">
                                    <span class="material-symbols-outlined"
                                        style="font-size: 14px; vertical-align: middle;">calendar_today</span>
                                    {{ $operations->first()->date->format('d/m/Y') }}
                                    @if ($operations->first()->reference)
                                        • Réf: {{ $operations->first()->reference }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <form action="{{ route('operations.destroy', [$journal, $numeroOperation]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btnDanger"
                                style="display: inline-flex; align-items: center; gap: 6px;"
                                data-confirm-delete="Êtes-vous sûr de vouloir supprimer l'opération N°{{ $numeroOperation }} ({{ $operations->count() }} lignes) ?">
                                <span class="material-symbols-outlined" style="font-size: 18px;">delete</span>
                                Supprimer
                            </button>
                        </form>
                    </div>

                    <div class="tableWrap">
                        <table class="table tableFluid">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>N° Compte</th>
                                    <th>Libellé</th>
                                    <th class="right">Débit (FCFA)</th>
                                    <th class="right">Crédit (FCFA)</th>
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
                                        <td class="mono">{{ $operation->date->format('d/m/Y') }}</td>
                                        <td><strong class="mono">{{ $operation->numero_compte_general }}</strong></td>
                                        <td>{{ $operation->libelle }}</td>
                                        <td class="right mono" style="color: #dc2626;">
                                            @if ($operation->debit)
                                                {{ number_format($operation->debit, 0, ',', ' ') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="right mono" style="color: #16a34a;">
                                            @if ($operation->credit)
                                                {{ number_format($operation->credit, 0, ',', ' ') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="right strong">TOTAUX</td>
                                    <td class="right strong mono" style="color: #dc2626;">
                                        {{ number_format($opDebit, 0, ',', ' ') }}
                                    </td>
                                    <td class="right strong mono" style="color: #16a34a;">
                                        {{ number_format($opCredit, 0, ',', ' ') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div style="margin-top: 12px;">
                        @if (abs($opDebit - $opCredit) < 0.01)
                            <span class="badgeOk">
                                <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
                                Opération équilibrée
                            </span>
                        @else
                            <span class="badgeWarn">
                                <span class="material-symbols-outlined" style="font-size: 14px;">warning</span>
                                Opération déséquilibrée (Diff: {{ number_format(abs($opDebit - $opCredit), 0, ',', ' ') }})
                            </span>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif
    </section>

    @push('styles')
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
        <style>
            .operationCard {
                margin-bottom: 20px;
            }

            .operationCardHeader {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 16px;
                margin-bottom: 16px;
                padding-bottom: 16px;
                border-bottom: 1px solid #e5e7eb;
            }

            .operationCardTitle {
                display: flex;
                align-items: flex-start;
                gap: 12px;
            }

            .operationCardTitle .material-symbols-outlined {
                font-size: 24px;
                margin-top: 2px;
            }

            @media (max-width: 640px) {
                .operationCardHeader {
                    flex-direction: column;
                }
            }
        </style>
    @endpush
@endsection
