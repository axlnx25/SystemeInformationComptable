@extends('layouts.app')

@section('title', 'Tableau de bord')

@section('content')
    <section class="page pageWide">
        <header class="pageHeader">
            <div>
                <h1 class="pageTitle">Tableau de bord</h1>
                <p class="muted">Bienvenue, {{ auth()->user()->name }} 👋</p>
            </div>
        </header>

        <!-- KPI Cards Grid -->
        <div class="dashboardGrid">
            <!-- Total Journals Card -->
            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(59, 130, 246, 0.1); color: #2563eb;">
                    <span class="material-symbols-outlined">book</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Total Journaux</div>
                    <div class="statCardValue">{{ $totalJournals }}</div>
                    <div class="statCardHint">
                        <span class="material-symbols-outlined" style="font-size: 14px;">check_circle</span>
                        {{ $balancedJournals }} équilibré(s)
                    </div>
                </div>
            </div>

            <!-- Total Operations Card -->
            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">
                    <span class="material-symbols-outlined">receipt_long</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Total Opérations</div>
                    <div class="statCardValue">{{ $totalOperations }}</div>
                    <div class="statCardHint">
                        <span class="material-symbols-outlined" style="font-size: 14px;">trending_up</span>
                        Écritures enregistrées
                    </div>
                </div>
            </div>

            <!-- Total Debit Card -->
            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(239, 68, 68, 0.1); color: #dc2626;">
                    <span class="material-symbols-outlined">arrow_upward</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Total Débit</div>
                    <div class="statCardValue mono">{{ number_format($totalDebit, 0, ',', ' ') }}</div>
                    <div class="statCardHint">FCFA</div>
                </div>
            </div>

            <!-- Total Credit Card -->
            <div class="statCard">
                <div class="statCardIcon" style="background: rgba(34, 197, 94, 0.1); color: #16a34a;">
                    <span class="material-symbols-outlined">arrow_downward</span>
                </div>
                <div class="statCardContent">
                    <div class="statCardLabel">Total Crédit</div>
                    <div class="statCardValue mono">{{ number_format($totalCredit, 0, ',', ' ') }}</div>
                    <div class="statCardHint">FCFA</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quickActionsGrid">
            <a href="{{ route('journals.new') }}" class="actionCard actionCardPrimary">
                <div class="actionCardIcon">
                    <span class="material-symbols-outlined">add_circle</span>
                </div>
                <div class="actionCardContent">
                    <div class="actionCardTitle">Nouveau Journal</div>
                    <div class="actionCardDesc">Créer un journal et saisir des opérations</div>
                </div>
                <div class="actionCardArrow">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </div>
            </a>

            <a href="{{ route('journals.index') }}" class="actionCard actionCardSecondary">
                <div class="actionCardIcon">
                    <span class="material-symbols-outlined">history</span>
                </div>
                <div class="actionCardContent">
                    <div class="actionCardTitle">Historique</div>
                    <div class="actionCardDesc">Consulter tous les journaux</div>
                </div>
                <div class="actionCardArrow">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </div>
            </a>

            @if (auth()->user()->is_admin)
                <a href="{{ route('users.index') }}" class="actionCard actionCardAccent">
                    <div class="actionCardIcon">
                        <span class="material-symbols-outlined">group</span>
                    </div>
                    <div class="actionCardContent">
                        <div class="actionCardTitle">Utilisateurs</div>
                        <div class="actionCardDesc">Gérer les utilisateurs</div>
                    </div>
                    <div class="actionCardArrow">
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </div>
                </a>
            @endif
        </div>

        <!-- Recent Journals -->
        @if ($recentJournals->isNotEmpty())
            <div class="panel">
                <div class="tableHeader">
                    <h2 class="panelTitle">
                        <span class="material-symbols-outlined"
                            style="vertical-align: middle; margin-right: 8px;">schedule</span>
                        Journaux Récents
                    </h2>
                    <a href="{{ route('journals.index') }}" class="link">
                        Voir tout
                        <span class="material-symbols-outlined"
                            style="font-size: 16px; vertical-align: middle;">arrow_forward</span>
                    </a>
                </div>

                <div class="tableWrap">
                    <table class="table tableFluid">
                        <thead>
                            <tr>
                                <th>Nom du journal</th>
                                <th class="right">Opérations</th>
                                <th class="right">Débit (FCFA)</th>
                                <th class="right">Crédit (FCFA)</th>
                                <th>État</th>
                                <th class="right">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentJournals as $journal)
                                <tr>
                                    <td>
                                        <div class="row gap" style="gap: 8px;">
                                            <span class="material-symbols-outlined"
                                                style="font-size: 20px; color: #6b7280;">book</span>
                                            <strong>{{ $journal->designation }}</strong>
                                        </div>
                                    </td>
                                    <td class="right">
                                        <span class="badge"
                                            style="background: rgba(59, 130, 246, 0.1); color: #2563eb; border-color: rgba(59, 130, 246, 0.3);">
                                            {{ $journal->operations_count }}
                                        </span>
                                    </td>
                                    <td class="right mono" style="color: #dc2626;">
                                        {{ number_format($journal->total_debit, 0, ',', ' ') }}
                                    </td>
                                    <td class="right mono" style="color: #16a34a;">
                                        {{ number_format($journal->total_credit, 0, ',', ' ') }}
                                    </td>
                                    <td>
                                        @if ($journal->is_balanced)
                                            <span class="badgeOk">
                                                <span class="material-symbols-outlined"
                                                    style="font-size: 14px;">check_circle</span>
                                                Équilibré
                                            </span>
                                        @else
                                            <span class="badgeWarn">
                                                <span class="material-symbols-outlined"
                                                    style="font-size: 14px;">warning</span>
                                                Déséquilibré
                                            </span>
                                        @endif
                                    </td>
                                    <td class="right">
                                        <div
                                            style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
                                            <a href="{{ route('journals.history', $journal) }}" class="iconBtn"
                                                title="Voir l'historique">
                                                <span class="material-symbols-outlined">visibility</span>
                                            </a>
                                            <a href="{{ route('journals.operations', $journal) }}"
                                                class="iconBtn iconBtnPrimary" title="Continuer la saisie">
                                                <span class="material-symbols-outlined">edit_note</span>
                                            </a>
                                            <form action="{{ route('journals.destroy', $journal) }}" method="POST"
                                                style="display: inline;"
                                                onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le journal « {{ $journal->designation }} » et toutes ses opérations ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="iconBtn iconBtnDanger"
                                                    title="Supprimer le journal">
                                                    <span class="material-symbols-outlined">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="panel">
                <div class="emptyState">
                    <div class="emptyStateIcon">
                        <span class="material-symbols-outlined">folder_open</span>
                    </div>
                    <h3 class="emptyStateTitle">Aucun journal pour le moment</h3>
                    <p class="emptyStateDesc">
                        Créez votre premier journal pour commencer à enregistrer vos opérations comptables.
                    </p>
                    <a href="{{ route('journals.new') }}" class="btnPrimary">
                        <span class="material-symbols-outlined"
                            style="font-size: 18px; vertical-align: middle; margin-right: 6px;">add_circle</span>
                        Créer mon premier journal
                    </a>
                </div>
            </div>
        @endif
    </section>

    @push('styles')
        <link rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    @endpush
@endsection
