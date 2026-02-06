<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_operation',
        'date',
        'reference',
        'libelle',
        'debit',
        'credit',
        'numero_compte_general',
        'journal_id'
    ];

    protected $casts = [
        'date' => 'date',
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    /**
     * Scope to filter operations by operation number
     */
    public function scopeByOperationNumber($query, string $numeroOperation)
    {
        return $query->where('numero_operation', $numeroOperation);
    }

    /**
     * Get the next available operation number for a journal
     */
    public static function getNextOperationNumber(int $journalId): string
    {
        $lastOperation = static::where('journal_id', $journalId)
            ->orderBy('numero_operation', 'desc')
            ->first();

        if (!$lastOperation) {
            return '1';
        }

        // Extract numeric part and increment
        $lastNumber = intval($lastOperation->numero_operation);
        return strval($lastNumber + 1);
    }

    /**
     * Get total debit and credit for a specific operation number
     */
    public static function getOperationTotal(int $journalId, string $numeroOperation): array
    {
        $operations = static::where('journal_id', $journalId)
            ->where('numero_operation', $numeroOperation)
            ->get();

        return [
            'debit' => $operations->sum('debit'),
            'credit' => $operations->sum('credit'),
            'lines' => $operations->count(),
        ];
    }

    /**
     * Check if an operation (group of lines) is balanced
     */
    public static function isBalanced(int $journalId, string $numeroOperation): bool
    {
        $totals = static::getOperationTotal($journalId, $numeroOperation);
        return bccomp($totals['debit'], $totals['credit'], 2) === 0;
    }
}
