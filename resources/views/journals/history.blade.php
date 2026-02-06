@extends('layouts.app')

@section('title', 'Historique - ' . $journal->designation)

@section('content')
    <section class="page pageWide">
        <header class="pageHeader">
            <div class="historyTop">
                <div class="journalTitleBar">
                    <a href="{{ route('journals.index') }}" class="md-link-back">
                        <span class="material-symbols-outlined">arrow_back</span>
                        <span>Retour aux journaux</span>
                    </a>
                    <div>
                        <h1 class="pageTitle">{{ $journal->designation }}</h1>
                        <p class="muted">Historique des opérations et état du journal</p>
                        <div class="md-chips-row">
                            <span class="md-chip md-chip-neutral">
                                <span class="material-symbols-outlined">schedule</span>
                                Créé le {{ $journal->created_at->format('d/m/Y') }}
                            </span>
                            <span class="md-chip {{ $journal->isBalanced() ? 'md-chip-success' : 'md-chip-warning' }}">
                                <span class="material-symbols-outlined">
                                    {{ $journal->isBalanced() ? 'check_circle' : 'warning' }}
                                </span>
                                {{ $journal->isBalanced() ? 'Journal équilibré' : 'Journal déséquilibré' }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="md-header-actions">
                    <a href="{{ route('journals.operations', $journal) }}" class="md-btn md-btn-primary">
                        <span class="material-symbols-outlined">add_circle</span>
                        <span>Nouvelle opération</span>
                    </a>
                    <form action="{{ route('journals.destroy', $journal) }}" method="POST"
                        data-debug-delete="journal-history"
                        data-journal-id="{{ $journal->id }}"
                        data-journal-name="{{ $journal->designation }}"
                        onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le journal « {{ $journal->designation }} » et toutes ses opérations ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="md-btn md-btn-danger" data-debug-delete-btn="journal-history">
                            <span class="material-symbols-outlined">delete</span>
                            <span>Supprimer le journal</span>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <!-- KPI Cards for Journal -->
        <div class="md-kpi-grid">
            <article class="md-card md-card-tonal">
                <div class="md-card-header">
                    <span class="material-symbols-outlined md-card-icon md-icon-debit">arrow_upward</span>
                    <div>
                        <div class="md-card-title">Total Débit</div>
                        <div class="md-card-subtitle">Montant cumulé</div>
                    </div>
                </div>
                <div class="md-card-value mono">
                    {{ number_format($totals['total_debit'], 0, ',', ' ') }} <span class="md-unit">FCFA</span>
                </div>
            </article>

            <article class="md-card md-card-tonal">
                <div class="md-card-header">
                    <span class="material-symbols-outlined md-card-icon md-icon-credit">arrow_downward</span>
                    <div>
                        <div class="md-card-title">Total Crédit</div>
                        <div class="md-card-subtitle">Montant cumulé</div>
                    </div>
                </div>
                <div class="md-card-value mono">
                    {{ number_format($totals['total_credit'], 0, ',', ' ') }} <span class="md-unit">FCFA</span>
                </div>
            </article>

            <article class="md-card md-card-outlined">
                <div class="md-card-header">
                    <span class="material-symbols-outlined md-card-icon md-icon-ops">receipt_long</span>
                    <div>
                        <div class="md-card-title">Opérations</div>
                        <div class="md-card-subtitle">Lignes comptables</div>
                    </div>
                </div>
                <div class="md-card-value">
                    {{ $totals['unique_operations'] }}
                    <span class="md-unit">opération(s)</span>
                </div>
                <div class="md-card-footer mono">
                    {{ $totals['operations_count'] }} ligne(s)
                </div>
            </article>

            <article class="md-card md-card-outlined">
                <div class="md-card-header">
                    <span
                        class="material-symbols-outlined md-card-icon {{ $journal->isBalanced() ? 'md-icon-success' : 'md-icon-warning' }}">
                        {{ $journal->isBalanced() ? 'check_circle' : 'warning' }}
                    </span>
                    <div>
                        <div class="md-card-title">État du journal</div>
                        <div class="md-card-subtitle">Équilibre Débit / Crédit</div>
                    </div>
                </div>
                <div class="md-card-value">
                    @if ($journal->isBalanced())
                        <span class="badgeOk">Équilibré</span>
                    @else
                        <span class="badgeWarn">Déséquilibré</span>
                    @endif
                </div>
            </article>
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
                        <form action="{{ route('operations.destroy', [$journal, $numeroOperation]) }}" method="POST"
                            style="display: inline-flex;"
                            data-debug-delete="operation-history"
                            data-journal-id="{{ $journal->id }}"
                            data-numero-operation="{{ $numeroOperation }}"
                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer l\'opération N°{{ $numeroOperation }} ({{ $operations->count() }} lignes) ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btnDanger"
                                style="display: inline-flex; align-items: center; gap: 6px;"
                                data-debug-delete-btn="operation-history">
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
            .md-link-back {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 6px 10px;
                border-radius: 999px;
                text-decoration: none;
                color: #4b5563;
                font-size: 13px;
                border: 1px solid rgba(148, 163, 184, 0.3);
                background: rgba(248, 250, 252, 0.9);
                transition: background 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
            }

            .md-link-back .material-symbols-outlined {
                font-size: 18px;
            }

            .md-link-back:hover {
                background: #ffffff;
                border-color: rgba(148, 163, 184, 0.6);
                box-shadow: 0 1px 3px rgba(15, 23, 42, 0.1);
            }

            .md-header-actions {
                display: flex;
                align-items: center;
                gap: 10px;
                flex-wrap: wrap;
            }

            .md-btn {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                border-radius: 999px;
                padding: 8px 14px;
                font-size: 13px;
                font-weight: 500;
                border: 1px solid transparent;
                background: transparent;
                cursor: pointer;
                text-decoration: none;
                transition: background 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease, transform 0.05s ease;
            }

            .md-btn .material-symbols-outlined {
                font-size: 18px;
            }

            .md-btn-primary {
                background: rgba(37, 99, 235, 0.1);
                color: #1d4ed8;
                border-color: rgba(37, 99, 235, 0.3);
            }

            .md-btn-primary:hover {
                background: rgba(37, 99, 235, 0.15);
                box-shadow: 0 1px 4px rgba(37, 99, 235, 0.35);
            }

            .md-btn-danger {
                background: rgba(220, 38, 38, 0.03);
                color: #b91c1c;
                border-color: rgba(220, 38, 38, 0.3);
            }

            .md-btn-danger:hover {
                background: rgba(220, 38, 38, 0.08);
                box-shadow: 0 1px 4px rgba(220, 38, 38, 0.3);
            }

            .md-btn:active {
                transform: translateY(1px);
                box-shadow: none;
            }

            .md-chips-row {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 8px;
            }

            .md-chip {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                border-radius: 999px;
                font-size: 12px;
                border: 1px solid rgba(148, 163, 184, 0.4);
                background: rgba(248, 250, 252, 0.8);
                color: #4b5563;
            }

            .md-chip .material-symbols-outlined {
                font-size: 16px;
            }

            .md-chip-neutral {
                background: rgba(248, 250, 252, 0.9);
            }

            .md-chip-success {
                border-color: rgba(22, 163, 74, 0.5);
                background: rgba(22, 163, 74, 0.05);
                color: #166534;
            }

            .md-chip-warning {
                border-color: rgba(245, 158, 11, 0.5);
                background: rgba(245, 158, 11, 0.05);
                color: #92400e;
            }

            .md-kpi-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
                margin-bottom: 20px;
            }

            .md-card {
                border-radius: 16px;
                padding: 14px 16px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .md-card-tonal {
                background: linear-gradient(135deg, #f9fafb, #eff6ff);
                border: 1px solid rgba(148, 163, 184, 0.4);
            }

            .md-card-outlined {
                background: #ffffff;
                border: 1px solid rgba(148, 163, 184, 0.3);
            }

            .md-card-header {
                display: flex;
                gap: 10px;
                align-items: center;
            }

            .md-card-icon {
                border-radius: 999px;
                padding: 6px;
                background: rgba(15, 23, 42, 0.03);
                font-size: 20px;
            }

            .md-icon-debit {
                color: #dc2626;
                background: rgba(239, 68, 68, 0.08);
            }

            .md-icon-credit {
                color: #16a34a;
                background: rgba(34, 197, 94, 0.08);
            }

            .md-icon-ops {
                color: #2563eb;
                background: rgba(37, 99, 235, 0.08);
            }

            .md-icon-success {
                color: #16a34a;
                background: rgba(34, 197, 94, 0.08);
            }

            .md-icon-warning {
                color: #d97706;
                background: rgba(245, 158, 11, 0.08);
            }

            .md-card-title {
                font-size: 13px;
                font-weight: 600;
                color: #111827;
            }

            .md-card-subtitle {
                font-size: 12px;
                color: #6b7280;
            }

            .md-card-value {
                font-size: 20px;
                font-weight: 600;
                color: #111827;
                display: flex;
                align-items: baseline;
                gap: 4px;
            }

            .md-card-footer {
                font-size: 12px;
                color: #6b7280;
            }

            .md-unit {
                font-size: 12px;
                color: #6b7280;
            }

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
