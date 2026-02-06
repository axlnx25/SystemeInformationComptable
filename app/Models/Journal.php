<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasFactory;

    protected $fillable = [
        'designation',
        'user_id',
    ];

    public function operations(): HasMany
    {
        return $this->hasMany(Operation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get operations grouped by numero_operation
     */
    public function getOperationsGrouped()
    {
        return $this->operations()
            ->orderBy('numero_operation')
            ->orderBy('id')
            ->get()
            ->groupBy('numero_operation');
    }

    /**
     * Get total debits and credits for the journal
     */
    public function getTotals(): array
    {
        $operations = $this->operations;

        return [
            'total_debit' => $operations->sum('debit'),
            'total_credit' => $operations->sum('credit'),
            'operations_count' => $operations->count(),
            'unique_operations' => $operations->pluck('numero_operation')->unique()->count(),
        ];
    }

    /**
     * Check if the journal is balanced (total debit = total credit)
     */
    public function isBalanced(): bool
    {
        $totals = $this->getTotals();
        return bccomp($totals['total_debit'], $totals['total_credit'], 2) === 0;
    }
}
