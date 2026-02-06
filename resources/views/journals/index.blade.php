@extends('layouts.app')

@section('title', 'Historique des journaux')

@section('content')
    <section class="page pageWide">
        <header class="pageHeader">
            <div class="historyTop">
                <div class="journalTitleBar">
                    <h1 class="pageTitle">Historique des journaux</h1>
                    <p class="muted">Retrouvez l’ensemble de vos journaux comptables</p>
                </div>

                <div class="md-header-actions">
                    <label class="searchBox">
                        <span class="searchIcon" aria-hidden="true">⌕</span>
                        <input class="searchInput" type="text" placeholder="Rechercher un journal..." id="search-input">
                    </label>
                    <a href="{{ route('journals.new') }}" class="md-btn md-btn-primary">
                        <span class="material-symbols-outlined">add_circle</span>
                        <span>Nouveau journal</span>
                    </a>
                </div>
            </div>
        </header>

        <div class="panel md-card-outlined">
            <div class="panelTitle" style="display:flex;align-items:center;justify-content:space-between;gap:8px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-outlined" style="font-size:20px;color:#4b5563;">filter_list</span>
                    <span>Filtres de recherche</span>
                </div>
                <span class="muted" style="font-size:12px;">
                    {{ $journals->count() }} journal(aux) affiché(s)
                </span>
            </div>
            <div class="filtersGrid">
                <label class="field">
                    <div class="fieldLabel">Titre du journal</div>
                    <input class="input" type="text" id="filter-name" placeholder="Ex: Journal de caisse">
                </label>

                <label class="field">
                    <div class="fieldLabel">Date de début</div>
                    <input class="input" type="date" id="filter-from">
                </label>

                <label class="field">
                    <div class="fieldLabel">Date de fin</div>
                    <input class="input" type="date" id="filter-to">
                </label>

                <div class="row gap filterActions" style="align-items:flex-end;">
                    <button class="md-btn" type="button" id="reset-filters">
                        <span class="material-symbols-outlined">restart_alt</span>
                        <span>Réinitialiser</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="panel md-card-outlined">
            <div class="tableHeader" style="margin-bottom:8px;">
                <h2 class="panelTitle" style="display:flex;align-items:center;gap:8px;">
                    <span class="material-symbols-outlined" style="font-size:20px;color:#2563eb;">book</span>
                    <span>Liste des journaux</span>
                </h2>
            </div>
            <div class="tableWrap">
                <table class="table tableFluid journals-table">
                    <thead>
                        <tr>
                            <th>Nom du journal</th>
                            <th>Créé le</th>
                            <th class="right">Total débit (FCFA)</th>
                            <th class="right">Total crédit (FCFA)</th>
                            <th class="right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="journals-tbody">
                        @forelse ($journals as $journal)
                            <tr data-name="{{ strtolower($journal->designation) }}"
                                data-created="{{ $journal->created_at->format('Y-m-d') }}">
                                <td>
                                    <div style="display:flex;align-items:center;gap:8px;">
                                        <span class="material-symbols-outlined"
                                            style="font-size:20px;color:#6b7280;">book</span>
                                        <div>
                                            <a class="link" href="{{ route('journals.history', $journal) }}">
                                                {{ $journal->designation }}
                                            </a>
                                            <div style="font-size:12px;color:#6b7280;">
                                                {{ $journal->created_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="mono">{{ $journal->created_at->format('d/m/Y') }}</td>
                                <td class="right mono">{{ number_format($journal->total_debit, 0, ',', ' ') }}</td>
                                <td class="right mono">{{ number_format($journal->total_credit, 0, ',', ' ') }}</td>
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
                                            style="display: inline-flex;"
                                            data-debug-delete="journal-index"
                                            data-journal-id="{{ $journal->id }}"
                                            data-journal-name="{{ $journal->designation }}"
                                            onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer le journal « {{ $journal->designation }} » et toutes ses opérations ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="iconBtn iconBtnDanger"
                                                title="Supprimer le journal"
                                                data-debug-delete-btn="journal-index">
                                                <span class="material-symbols-outlined">delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="emptyState">
                                        <div class="emptyStateIcon">
                                            <span class="material-symbols-outlined">folder_open</span>
                                        </div>
                                        <h3 class="emptyStateTitle">Aucun journal pour le moment</h3>
                                        <p class="emptyStateDesc">
                                            Créez votre premier journal pour commencer à enregistrer vos opérations.
                                        </p>
                                        <a href="{{ route('journals.new') }}" class="md-btn md-btn-primary">
                                            <span class="material-symbols-outlined">add_circle</span>
                                            <span>Nouveau journal</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @push('styles')
        <style>
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

            .md-card-outlined {
                border-radius: 16px;
                border: 1px solid rgba(148, 163, 184, 0.35);
            }

            /* Mise en forme des colonnes du tableau des journaux */
            .journals-table {
                table-layout: fixed;
            }

            .journals-table th:nth-child(1),
            .journals-table td:nth-child(1) {
                width: 38%;
            }

            .journals-table th:nth-child(2),
            .journals-table td:nth-child(2) {
                width: 14%;
            }

            .journals-table th:nth-child(3),
            .journals-table td:nth-child(3),
            .journals-table th:nth-child(4),
            .journals-table td:nth-child(4) {
                width: 16%;
            }

            .journals-table th:nth-child(5),
            .journals-table td:nth-child(5) {
                width: 16%;
            }

            .journals-table td {
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .journals-table td:first-child {
                white-space: normal;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search-input');
                const filterName = document.getElementById('filter-name');
                const filterFrom = document.getElementById('filter-from');
                const filterTo = document.getElementById('filter-to');
                const resetBtn = document.getElementById('reset-filters');
                const tbody = document.getElementById('journals-tbody');
                const rows = tbody.querySelectorAll('tr[data-name]');

                function filterJournals() {
                    const query = (searchInput.value || filterName.value).toLowerCase().trim();
                    const from = filterFrom.value;
                    const to = filterTo.value;

                    rows.forEach(row => {
                        const name = row.dataset.name;
                        const created = row.dataset.created;

                        let show = true;

                        if (query && !name.includes(query)) {
                            show = false;
                        }

                        if (from && created < from) {
                            show = false;
                        }

                        if (to && created > to) {
                            show = false;
                        }

                        row.style.display = show ? '' : 'none';
                    });
                }

                searchInput.addEventListener('input', filterJournals);
                filterName.addEventListener('input', function() {
                    searchInput.value = this.value;
                    filterJournals();
                });
                filterFrom.addEventListener('change', filterJournals);
                filterTo.addEventListener('change', filterJournals);

                resetBtn.addEventListener('click', function() {
                    searchInput.value = '';
                    filterName.value = '';
                    filterFrom.value = '';
                    filterTo.value = '';
                    filterJournals();
                });
            });
        </script>
    @endpush
@endsection
