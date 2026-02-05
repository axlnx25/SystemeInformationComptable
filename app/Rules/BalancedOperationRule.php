<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Operation;

class BalancedOperationRule implements ValidationRule
{
    protected int $journalId;
    protected string $numeroOperation;

    public function __construct(int $journalId, string $numeroOperation)
    {
        $this->journalId = $journalId;
        $this->numeroOperation = $numeroOperation;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if the operation is balanced
        if (!Operation::isBalanced($this->journalId, $this->numeroOperation)) {
            $totals = Operation::getOperationTotal($this->journalId, $this->numeroOperation);
            $fail("L'opération n°{$this->numeroOperation} n'est pas équilibrée. Débit: {$totals['debit']}, Crédit: {$totals['credit']}");
        }
    }
}
