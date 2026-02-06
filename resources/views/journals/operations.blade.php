@extends('layouts.app')

@section('title', 'Saisir Opérations')
@section('page-title', $journal->designation)

@section('content')
    <section class="page pageWide">
        <header class="pageHeader">
            <div class="journalTitleBar">
                <h1 class="pageTitle">{{ $journal->designation }}</h1>
            </div>
        </header>

        <div class="panel">
            <div class="tableHeader">
                <h2 class="panelTitle">Opérations</h2>
                <div class="row gap">
                    <a href="{{ route('journals.index') }}" class="btn">Retour</a>
                    <button type="button" id="save-journal-btn" class="btnPrimary">Enregistrer le journal</button>
                </div>
            </div>

            <div id="error-message" class="alert" style="display: none;"></div>
            <div id="success-message" class="success" style="display: none;"></div>

            <form id="operation-form" action="{{ route('operations.storeBatch', $journal) }}" method="POST">
                @csrf
                <input type="hidden" name="journal_id" value="{{ $journal->id }}">

                <div class="tableWrap">
                    <table class="table">
                        <thead>
                            <!-- Draft Row (ligne de saisie) -->
                            <tr class="draftRow">
                                <th>
                                    <input type="date" id="draft-date" class="input" required>
                                </th>
                                <th class="mono" id="draft-numero">{{ $nextOperationNumber }}</th>
                                <th>
                                    <input type="text" id="draft-reference" class="input" placeholder="Ex: FAC-001">
                                </th>
                                <th>
                                    <input type="text" id="draft-compte" class="input" placeholder="Ex: 512" required>
                                </th>
                                <th>
                                    <input type="text" id="draft-libelle" class="input"
                                        placeholder="Libellé de l'écriture" required>
                                </th>
                                <th>
                                    <input type="number" id="draft-debit" class="input right" placeholder="0.00"
                                        step="0.01" min="0">
                                </th>
                                <th>
                                    <input type="number" id="draft-credit" class="input right" placeholder="0.00"
                                        step="0.01" min="0">
                                </th>
                                <th class="right">
                                    <button type="button" id="add-line-btn" class="btnPrimary">Ajouter</button>
                                </th>
                            </tr>
                            <!-- Header Row -->
                            <tr>
                                <th>Date</th>
                                <th>N°</th>
                                <th>Réf. pièce</th>
                                <th>N° compte</th>
                                <th>Libellé</th>
                                <th class="right">Débit (FCFA)</th>
                                <th class="right">Crédit (FCFA)</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="operations-tbody">
                            <!-- Les lignes ajoutées apparaissent ici -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5" class="right strong">Totaux</td>
                                <td class="right strong mono" id="total-debit">0</td>
                                <td class="right strong mono" id="total-credit">0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="row between" style="margin-top: 12px;">
                    <div id="balance-badge" class="badgeWarn">Non équilibré</div>
                    <div class="muted">Règle: Débit total = Crédit total.</div>
                </div>
            </form>
        </div>

        <!-- KPI Cards -->
        <div class="kpiGrid">
            <div class="kpiCard">
                <div class="kpiLabel">Total débit</div>
                <div class="kpiValue mono"><span id="kpi-debit">0</span> FCFA</div>
                <div class="kpiHint">Correctement imputé</div>
            </div>
            <div class="kpiCard">
                <div class="kpiLabel">Total crédit</div>
                <div class="kpiValue mono"><span id="kpi-credit">0</span> FCFA</div>
                <div class="kpiHint">Correctement imputé</div>
            </div>
            <div class="kpiCard">
                <div class="kpiLabel">Solde de la période</div>
                <div class="kpiValue" id="kpi-status">Non équilibré</div>
                <div class="kpiHint mono">Différence: <span id="kpi-diff">0</span> FCFA</div>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('operation-form');
                const tbody = document.getElementById('operations-tbody');
                const addBtn = document.getElementById('add-line-btn');
                const saveBtn = document.getElementById('save-journal-btn');
                const errorDiv = document.getElementById('error-message');
                const successDiv = document.getElementById('success-message');

                // Draft inputs
                const draftDate = document.getElementById('draft-date');
                const draftNumero = document.getElementById('draft-numero');
                const draftReference = document.getElementById('draft-reference');
                const draftCompte = document.getElementById('draft-compte');
                const draftLibelle = document.getElementById('draft-libelle');
                const draftDebit = document.getElementById('draft-debit');
                const draftCredit = document.getElementById('draft-credit');

                // Totals
                const totalDebitEl = document.getElementById('total-debit');
                const totalCreditEl = document.getElementById('total-credit');
                const balanceBadge = document.getElementById('balance-badge');
                const kpiDebit = document.getElementById('kpi-debit');
                const kpiCredit = document.getElementById('kpi-credit');
                const kpiStatus = document.getElementById('kpi-status');
                const kpiDiff = document.getElementById('kpi-diff');

                let operations = [];
                let nextNumero = {{ $nextOperationNumber }};

                // Set today's date as default
                const today = new Date().toISOString().split('T')[0];
                draftDate.value = today;

                // Prevent both debit and credit from being filled
                draftDebit.addEventListener('input', function() {
                    if (this.value) draftCredit.value = '';
                });

                draftCredit.addEventListener('input', function() {
                    if (this.value) draftDebit.value = '';
                });

                function formatMoney(value) {
                    return new Intl.NumberFormat('fr-FR', {
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 2
                    }).format(value);
                }

                function calculateTotals() {
                    const debit = operations.reduce((sum, op) => sum + parseFloat(op.debit || 0), 0);
                    const credit = operations.reduce((sum, op) => sum + parseFloat(op.credit || 0), 0);
                    const diff = Math.abs(debit - credit);
                    const balanced = diff < 0.01;

                    totalDebitEl.textContent = formatMoney(debit);
                    totalCreditEl.textContent = formatMoney(credit);
                    kpiDebit.textContent = formatMoney(debit);
                    kpiCredit.textContent = formatMoney(credit);
                    kpiDiff.textContent = formatMoney(diff);

                    if (balanced) {
                        balanceBadge.textContent = 'Équilibré';
                        balanceBadge.className = 'badgeOk';
                        kpiStatus.textContent = 'Équilibré';
                    } else {
                        balanceBadge.textContent = 'Non équilibré';
                        balanceBadge.className = 'badgeWarn';
                        kpiStatus.textContent = 'Déséquilibré';
                    }

                    return {
                        debit,
                        credit,
                        balanced
                    };
                }

                function hasDebit(numero) {
                    return operations.some(op => op.numero == numero && parseFloat(op.debit || 0) > 0);
                }

                function hasCredit(numero) {
                    return operations.some(op => op.numero == numero && parseFloat(op.credit || 0) > 0);
                }

                function makeCounterpart(line, side) {
                    const base = {
                        date: line.date,
                        numero: line.numero,
                        reference: line.reference,
                        libelle: line.libelle
                    };

                    if (side === 'debit') {
                        return {
                            ...base,
                            compte: '',
                            debit: line.credit || '',
                            credit: ''
                        };
                    }

                    return {
                        ...base,
                        compte: '',
                        debit: '',
                        credit: line.debit || ''
                    };
                }

                function renderOperations() {
                    tbody.innerHTML = '';
                    operations.forEach((op, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                <td><input type="date" class="inputCell" value="${op.date}" data-index="${index}" data-field="date"></td>
                <td class="mono">${op.numero}</td>
                <td><input type="text" class="inputCell" value="${op.reference}" data-index="${index}" data-field="reference" placeholder="Ex: FAC-001"></td>
                <td><input type="text" class="inputCell" value="${op.compte}" data-index="${index}" data-field="compte" placeholder="Ex: 512"></td>
                <td><input type="text" class="inputCell" value="${op.libelle}" data-index="${index}" data-field="libelle" placeholder="Ex: Vente marchandises"></td>
                <td><input type="number" class="inputCell right" value="${op.debit || ''}" data-index="${index}" data-field="debit" placeholder="0" step="0.01" min="0"></td>
                <td><input type="number" class="inputCell right" value="${op.credit || ''}" data-index="${index}" data-field="credit" placeholder="0" step="0.01" min="0"></td>
                <td class="right">
                    <button type="button" class="btnDanger" onclick="removeOperation(${index})">Supprimer</button>
                </td>
            `;
                        tbody.appendChild(row);
                    });

                    // Add event listeners for inline editing
                    tbody.querySelectorAll('.inputCell').forEach(input => {
                        input.addEventListener('input', function() {
                            const index = parseInt(this.dataset.index);
                            const field = this.dataset.field;
                            operations[index][field] = this.value;

                            // Prevent both debit and credit
                            if (field === 'debit' && this.value) {
                                operations[index].credit = '';
                                this.closest('tr').querySelector('[data-field="credit"]').value = '';
                            } else if (field === 'credit' && this.value) {
                                operations[index].debit = '';
                                this.closest('tr').querySelector('[data-field="debit"]').value = '';
                            }

                            calculateTotals();
                        });
                    });

                    calculateTotals();
                }

                window.removeOperation = function(index) {
                    if (confirm('Supprimer cette ligne ?')) {
                        operations.splice(index, 1);
                        renderOperations();
                    }
                };

                function validateLine(line) {
                    if (!line.date) return 'Chaque ligne doit avoir une date.';
                    if (!line.compte.trim()) return 'Chaque ligne doit avoir un numéro de compte.';
                    if (!line.libelle.trim()) return 'Chaque ligne doit avoir un libellé.';

                    const debit = parseFloat(line.debit || 0);
                    const credit = parseFloat(line.credit || 0);

                    if (isNaN(debit) || isNaN(credit)) return 'Débit/Crédit doivent être numériques.';
                    if (debit < 0 || credit < 0) return 'Débit/Crédit ne peuvent pas être négatifs.';
                    if (debit > 0 && credit > 0) return 'Une ligne ne doit pas avoir Débit et Crédit en même temps.';
                    if (debit === 0 && credit === 0) return 'Chaque ligne doit avoir un débit ou un crédit.';

                    return '';
                }

                addBtn.addEventListener('click', function() {
                    const line = {
                        date: draftDate.value,
                        numero: parseInt(draftNumero.textContent),
                        reference: draftReference.value,
                        compte: draftCompte.value,
                        libelle: draftLibelle.value,
                        debit: draftDebit.value,
                        credit: draftCredit.value
                    };

                    const error = validateLine(line);
                    if (error) {
                        errorDiv.textContent = error;
                        errorDiv.style.display = 'block';
                        successDiv.style.display = 'none';
                        return;
                    }

                    errorDiv.style.display = 'none';
                    operations.push(line);
                    renderOperations();

                    // Auto-fill logic
                    const numero = line.numero;
                    const debitSide = parseFloat(line.debit || 0) > 0;
                    const creditSide = parseFloat(line.credit || 0) > 0;
                    const nowHasDebit = hasDebit(numero);
                    const nowHasCredit = hasCredit(numero);

                    if (debitSide && !nowHasCredit) {
                        // Create credit counterpart
                        const counterpart = makeCounterpart(line, 'credit');
                        draftDate.value = counterpart.date;
                        draftReference.value = counterpart.reference;
                        draftLibelle.value = counterpart.libelle;
                        draftCompte.value = '';
                        draftDebit.value = '';
                        draftCredit.value = counterpart.credit;
                        draftCompte.focus();
                    } else if (creditSide && !nowHasDebit) {
                        // Create debit counterpart
                        const counterpart = makeCounterpart(line, 'debit');
                        draftDate.value = counterpart.date;
                        draftReference.value = counterpart.reference;
                        draftLibelle.value = counterpart.libelle;
                        draftCompte.value = '';
                        draftDebit.value = counterpart.debit;
                        draftCredit.value = '';
                        draftCompte.focus();
                    } else {
                        // Operation complete, move to next numero
                        nextNumero++;
                        draftNumero.textContent = nextNumero;
                        draftDate.value = today;
                        draftReference.value = '';
                        draftCompte.value = '';
                        draftLibelle.value = '';
                        draftDebit.value = '';
                        draftCredit.value = '';
                        draftDate.focus();
                    }
                });

                saveBtn.addEventListener('click', function() {
                    if (operations.length === 0) {
                        errorDiv.textContent = 'Ajoute au moins une opération.';
                        errorDiv.style.display = 'block';
                        successDiv.style.display = 'none';
                        return;
                    }

                    const totals = calculateTotals();
                    if (!totals.balanced) {
                        errorDiv.textContent =
                            `Journal non équilibré (Débit ${formatMoney(totals.debit)} / Crédit ${formatMoney(totals.credit)}).`;
                        errorDiv.style.display = 'block';
                        successDiv.style.display = 'none';
                        return;
                    }

                    // Prepare form data
                    const formData = new FormData(form);
                    operations.forEach((op, index) => {
                        formData.append(`operations[${index}][date]`, op.date);
                        formData.append(`operations[${index}][numero_operation]`, op.numero);
                        formData.append(`operations[${index}][reference]`, op.reference);
                        formData.append(`operations[${index}][numero_compte_general]`, op.compte);
                        formData.append(`operations[${index}][libelle]`, op.libelle);
                        formData.append(`operations[${index}][debit]`, op.debit || 0);
                        formData.append(`operations[${index}][credit]`, op.credit || 0);
                    });

                    // Submit form
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                successDiv.textContent =
                                    'Journal enregistré avec succès ! Vous pouvez le voir dans l\'historique.';
                                successDiv.style.display = 'block';
                                errorDiv.style.display = 'none';

                                // Reset
                                operations = [];
                                renderOperations();
                                nextNumero = 1;
                                draftNumero.textContent = nextNumero;
                                draftDate.value = today;
                                draftReference.value = '';
                                draftCompte.value = '';
                                draftLibelle.value = '';
                                draftDebit.value = '';
                                draftCredit.value = '';
                            } else {
                                errorDiv.textContent = data.message || 'Erreur lors de l\'enregistrement.';
                                errorDiv.style.display = 'block';
                                successDiv.style.display = 'none';
                            }
                        })
                        .catch(error => {
                            errorDiv.textContent = 'Erreur réseau : ' + error.message;
                            errorDiv.style.display = 'block';
                            successDiv.style.display = 'none';
                        });
                });
            });
        </script>
    @endpush
@endsection
