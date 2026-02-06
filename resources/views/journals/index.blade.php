@extends('layouts.app')

@section('title', 'Historique des journaux')

@section('content')
    <section class="page pageWide">
        <header class="pageHeader">
            <div class="historyTop">
                <div class="journalTitleBar">
                    <h1 class="pageTitle">Historique des journaux</h1>
                </div>

                <label class="searchBox">
                    <span class="searchIcon" aria-hidden="true">⌕</span>
                    <input class="searchInput" type="text" placeholder="Rechercher un journal..." id="search-input">
                </label>
            </div>
        </header>

        <div class="panel">
            <div class="panelTitle">Filtres de recherche</div>
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

                <div class="row gap filterActions">
                    <button class="btnReset" type="button" id="reset-filters">
                        Réinitialiser
                    </button>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="tableWrap">
                <table class="table tableFluid">
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
                                    <a class="link" href="{{ route('journals.history', $journal) }}">
                                        {{ $journal->designation }}
                                    </a>
                                </td>
                                <td class="mono">{{ $journal->created_at->format('d/m/Y') }}</td>
                                <td class="right mono">{{ number_format($journal->total_debit, 0, ',', ' ') }}</td>
                                <td class="right mono">{{ number_format($journal->total_credit, 0, ',', ' ') }}</td>
                                <td class="right">
                                    <div style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
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
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="muted" style="padding: 12px;">
                                        Aucun journal.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

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
