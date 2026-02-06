/**
 * Application JavaScript - Comptabilité
 * Gestion des interactions et validations
 */

// ========================================
// UTILITIES
// ========================================

/**
 * Format number as currency
 */
function formatCurrency(value) {
    return new Intl.NumberFormat('fr-FR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}

/**
 * Parse currency string to number
 */
function parseCurrency(value) {
    if (!value) return 0;
    return parseFloat(value.toString().replace(/\s/g, '').replace(',', '.')) || 0;
}

/**
 * Show toast notification
 */
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} animate-slide-in`;
    toast.style.position = 'fixed';
    toast.style.top = '20px';
    toast.style.right = '20px';
    toast.style.zIndex = '9999';
    toast.style.minWidth = '300px';
    toast.textContent = message;

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ========================================
// OPERATION FORM MANAGEMENT
// ========================================

class OperationForm {
    constructor(formId) {
        this.form = document.getElementById(formId);
        if (!this.form) return;

        this.linesContainer = document.getElementById('operation-lines');
        this.addLineBtn = document.getElementById('add-line-btn');
        this.submitBtn = document.getElementById('submit-btn');
        this.totalDebitEl = document.getElementById('total-debit');
        this.totalCreditEl = document.getElementById('total-credit');
        this.differenceEl = document.getElementById('difference');
        this.balanceStatusEl = document.getElementById('balance-status');

        this.lines = [];
        this.lineCounter = 0;

        this.init();
    }

    init() {
        // Add first line
        this.addLine();

        // Event listeners
        this.addLineBtn?.addEventListener('click', () => this.addLine());
        this.form?.addEventListener('submit', (e) => this.handleSubmit(e));

        // Auto-save to localStorage
        this.form?.addEventListener('input', () => this.saveToLocalStorage());
        this.loadFromLocalStorage();
    }

    addLine(prefillData = null) {
        const lineIndex = this.lineCounter++;
        const isFirstLine = this.lines.length === 0;

        // Get data from first line for auto-fill
        const firstLineData = this.getFirstLineData();
        const shouldPrefill = !isFirstLine && firstLineData;

        const lineDiv = document.createElement('div');
        lineDiv.className = 'operation-line card animate-fade-in';
        lineDiv.dataset.lineIndex = lineIndex;

        lineDiv.innerHTML = `
      <div class="flex justify-between items-center mb-3">
        <h4 class="text-sm font-semibold" style="margin: 0;">Ligne ${this.lines.length + 1}</h4>
        ${!isFirstLine ? '<button type="button" class="btn btn-danger btn-sm remove-line-btn">Supprimer</button>' : ''}
      </div>
      
      <div class="grid grid-cols-2 gap-3">
        <div class="form-group">
          <label class="form-label">Date</label>
          <input type="date" 
                 name="lines[${lineIndex}][date]" 
                 class="form-control line-date" 
                 value="${shouldPrefill ? firstLineData.date : ''}"
                 ${shouldPrefill ? 'readonly' : ''}
                 required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Numéro Opération</label>
          <input type="text" 
                 name="lines[${lineIndex}][numero_operation]" 
                 class="form-control line-numero" 
                 value="${shouldPrefill ? firstLineData.numero_operation : ''}"
                 ${shouldPrefill ? 'readonly' : ''}
                 required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Référence Pièce</label>
          <input type="text" 
                 name="lines[${lineIndex}][reference]" 
                 class="form-control line-reference" 
                 value="${shouldPrefill ? firstLineData.reference : ''}"
                 ${shouldPrefill ? 'readonly' : ''}>
        </div>
        
        <div class="form-group">
          <label class="form-label">N° Compte Général</label>
          <input type="text" 
                 name="lines[${lineIndex}][numero_compte_general]" 
                 class="form-control line-compte" 
                 required>
        </div>
        
        <div class="form-group" style="grid-column: span 2;">
          <label class="form-label">Libellé</label>
          <input type="text" 
                 name="lines[${lineIndex}][libelle]" 
                 class="form-control line-libelle" 
                 value="${shouldPrefill ? firstLineData.libelle : ''}"
                 ${shouldPrefill ? 'readonly' : ''}
                 required>
        </div>
        
        <div class="form-group">
          <label class="form-label">Débit</label>
          <input type="number" 
                 step="0.01" 
                 min="0"
                 name="lines[${lineIndex}][debit]" 
                 class="form-control line-debit" 
                 placeholder="0.00">
        </div>
        
        <div class="form-group">
          <label class="form-label">Crédit</label>
          <input type="number" 
                 step="0.01" 
                 min="0"
                 name="lines[${lineIndex}][credit]" 
                 class="form-control line-credit" 
                 placeholder="0.00">
        </div>
      </div>
    `;

        this.linesContainer.appendChild(lineDiv);
        this.lines.push(lineDiv);

        // Add event listeners
        const removeBtn = lineDiv.querySelector('.remove-line-btn');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => this.removeLine(lineDiv));
        }

        // Add input listeners for validation
        const debitInput = lineDiv.querySelector('.line-debit');
        const creditInput = lineDiv.querySelector('.line-credit');

        debitInput?.addEventListener('input', () => {
            if (parseFloat(debitInput.value) > 0) {
                creditInput.value = '';
                creditInput.disabled = true;
            } else {
                creditInput.disabled = false;
            }
            this.updateTotals();
        });

        creditInput?.addEventListener('input', () => {
            if (parseFloat(creditInput.value) > 0) {
                debitInput.value = '';
                debitInput.disabled = true;
            } else {
                debitInput.disabled = false;
            }
            this.updateTotals();
        });

        // Update totals on any input
        lineDiv.querySelectorAll('input').forEach(input => {
            input.addEventListener('input', () => this.updateTotals());
        });
    }

    removeLine(lineDiv) {
        const index = this.lines.indexOf(lineDiv);
        if (index > -1) {
            this.lines.splice(index, 1);
            lineDiv.remove();
            this.updateLineNumbers();
            this.updateTotals();
        }
    }

    updateLineNumbers() {
        this.lines.forEach((line, index) => {
            const header = line.querySelector('h4');
            if (header) {
                header.textContent = `Ligne ${index + 1}`;
            }
        });
    }

    getFirstLineData() {
        if (this.lines.length === 0) return null;

        const firstLine = this.lines[0];
        return {
            date: firstLine.querySelector('.line-date')?.value || '',
            numero_operation: firstLine.querySelector('.line-numero')?.value || '',
            reference: firstLine.querySelector('.line-reference')?.value || '',
            libelle: firstLine.querySelector('.line-libelle')?.value || ''
        };
    }

    updateTotals() {
        let totalDebit = 0;
        let totalCredit = 0;

        this.lines.forEach(line => {
            const debit = parseCurrency(line.querySelector('.line-debit')?.value);
            const credit = parseCurrency(line.querySelector('.line-credit')?.value);

            totalDebit += debit;
            totalCredit += credit;
        });

        const difference = Math.abs(totalDebit - totalCredit);
        const isBalanced = difference < 0.01; // Tolerance for floating point

        // Update display
        if (this.totalDebitEl) {
            this.totalDebitEl.textContent = formatCurrency(totalDebit);
        }

        if (this.totalCreditEl) {
            this.totalCreditEl.textContent = formatCurrency(totalCredit);
        }

        if (this.differenceEl) {
            this.differenceEl.textContent = formatCurrency(difference);
            this.differenceEl.className = `total-value ${isBalanced ? 'balanced' : 'unbalanced'}`;
        }

        if (this.balanceStatusEl) {
            if (isBalanced) {
                this.balanceStatusEl.innerHTML = '<span class="badge badge-success">✓ Équilibré</span>';
            } else {
                this.balanceStatusEl.innerHTML = '<span class="badge badge-error">✗ Non équilibré</span>';
            }
        }

        // Enable/disable submit button
        if (this.submitBtn) {
            this.submitBtn.disabled = !isBalanced || this.lines.length < 2;

            if (!isBalanced && this.lines.length >= 2) {
                this.submitBtn.title = 'L\'opération doit être équilibrée';
            } else if (this.lines.length < 2) {
                this.submitBtn.title = 'Une opération doit avoir au moins 2 lignes';
            } else {
                this.submitBtn.title = '';
            }
        }
    }

    handleSubmit(e) {
        e.preventDefault();

        // Validate
        const formData = new FormData(this.form);
        const operations = [];

        this.lines.forEach((line, index) => {
            const lineData = {
                date: line.querySelector('.line-date')?.value,
                numero_operation: line.querySelector('.line-numero')?.value,
                reference: line.querySelector('.line-reference')?.value,
                numero_compte_general: line.querySelector('.line-compte')?.value,
                libelle: line.querySelector('.line-libelle')?.value,
                debit: parseCurrency(line.querySelector('.line-debit')?.value),
                credit: parseCurrency(line.querySelector('.line-credit')?.value)
            };

            // Validate debit/credit exclusivity
            if (lineData.debit > 0 && lineData.credit > 0) {
                showToast(`Ligne ${index + 1}: Une ligne ne peut pas avoir à la fois un débit et un crédit`, 'error');
                return;
            }

            if (lineData.debit === 0 && lineData.credit === 0) {
                showToast(`Ligne ${index + 1}: Une ligne doit avoir soit un débit, soit un crédit`, 'error');
                return;
            }

            operations.push(lineData);
        });

        if (operations.length < 2) {
            showToast('Une opération doit avoir au moins 2 lignes', 'error');
            return;
        }

        // Submit
        this.form.submit();
        this.clearLocalStorage();
    }

    saveToLocalStorage() {
        const data = [];
        this.lines.forEach(line => {
            data.push({
                date: line.querySelector('.line-date')?.value,
                numero_operation: line.querySelector('.line-numero')?.value,
                reference: line.querySelector('.line-reference')?.value,
                numero_compte_general: line.querySelector('.line-compte')?.value,
                libelle: line.querySelector('.line-libelle')?.value,
                debit: line.querySelector('.line-debit')?.value,
                credit: line.querySelector('.line-credit')?.value
            });
        });

        localStorage.setItem('operation_draft', JSON.stringify(data));
    }

    loadFromLocalStorage() {
        const saved = localStorage.getItem('operation_draft');
        if (!saved) return;

        try {
            const data = JSON.parse(saved);
            // Implementation for restoring saved data
            // (Optional feature)
        } catch (e) {
            console.error('Failed to load draft:', e);
        }
    }

    clearLocalStorage() {
        localStorage.removeItem('operation_draft');
    }
}

// ========================================
// CONFIRMATION DIALOGS
// ========================================

function confirmDelete(message = 'Êtes-vous sûr de vouloir supprimer cet élément ?') {
    return confirm(message);
}

// ========================================
// SIDEBAR TOGGLE (Mobile)
// ========================================

function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    if (sidebar) {
        sidebar.classList.toggle('open');
    }
}

// ========================================
// INITIALIZE ON DOM READY
// ========================================

document.addEventListener('DOMContentLoaded', function () {
    // Initialize operation form if present
    if (document.getElementById('operation-form')) {
        new OperationForm('operation-form');
    }

    // Add confirmation to delete buttons
    document.querySelectorAll('[data-confirm-delete]').forEach(btn => {
        btn.addEventListener('click', function (e) {
            if (!confirmDelete(this.dataset.confirmDelete)) {
                e.preventDefault();
            }
        });
    });

    // Auto-hide alerts after 5 seconds
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});
