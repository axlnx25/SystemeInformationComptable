<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Journal;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Operation::whereHas('journal', function ($query) {
            $query->where('user_id', Auth::id());
        });

        // Optional: group by numero_operation if requested
        if ($request->query('grouped') === 'true') {
            $operations = $query->orderBy('numero_operation')->orderBy('id')->get()->groupBy('numero_operation');
        } else {
            $operations = $query->get();
        }

        return response()->json($operations);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOperationRequest $request)
    {
        $data = $request->validated();
        
        // Auto-generate numero_operation if not provided
        if (empty($data['numero_operation'])) {
            $data['numero_operation'] = Operation::getNextOperationNumber($data['journal_id']);
        }

        $operation = Operation::create($data);

        return response()->json($operation, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Operation $operation)
    {
        $this->authorize('view', $operation);

        return response()->json($operation);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Operation $operation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateOperationRequest $request, Operation $operation)
    {
        $this->authorize('update', $operation);

        $operation->update($request->validated());

        return response()->json($operation);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Operation $operation)
    {
        $this->authorize('delete', $operation);

        $operation->delete();

        return response()->json(['message' => 'Operation deleted']);
    }

    /**
     * Store multiple operations at once (batch creation)
     */
    public function storeBatch(Request $request)
    {
        $request->validate([
            'operations' => 'required|array|min:2',
            'operations.*.numero_operation' => 'required|string|max:255',
            'operations.*.date' => 'required|date',
            'operations.*.reference' => 'nullable|string|max:255',
            'operations.*.libelle' => 'required|string|max:255',
            'operations.*.debit' => 'nullable|numeric|min:0',
            'operations.*.credit' => 'nullable|numeric|min:0',
            'operations.*.numero_compte_general' => 'required|string|max:255',
            'operations.*.journal_id' => 'required|exists:journals,id',
        ]);

        $operations = [];
        $journalId = null;
        $numeroOperation = null;

        // Validate that all operations belong to the same journal and have the same numero_operation
        foreach ($request->operations as $opData) {
            if ($journalId === null) {
                $journalId = $opData['journal_id'];
                $numeroOperation = $opData['numero_operation'];
            }

            if ($opData['journal_id'] !== $journalId) {
                return response()->json(['error' => 'Toutes les opérations doivent appartenir au même journal.'], 422);
            }

            if ($opData['numero_operation'] !== $numeroOperation) {
                return response()->json(['error' => 'Toutes les lignes doivent avoir le même numéro d\'opération.'], 422);
            }

            // Validate debit/credit exclusivity
            $debit = $opData['debit'] ?? 0;
            $credit = $opData['credit'] ?? 0;

            if ($debit > 0 && $credit > 0) {
                return response()->json(['error' => 'Une ligne ne peut pas avoir à la fois un débit et un crédit.'], 422);
            }

            if ($debit == 0 && $credit == 0) {
                return response()->json(['error' => 'Une ligne doit avoir soit un débit, soit un crédit.'], 422);
            }
        }

        // Check if user owns the journal
        $journal = Auth::user()->journals()->find($journalId);
        if (!$journal) {
            return response()->json(['error' => 'Journal non trouvé ou non autorisé.'], 403);
        }

        // Create all operations
        foreach ($request->operations as $opData) {
            $operations[] = Operation::create($opData);
        }

        // Validate balance
        if (!Operation::isBalanced($journalId, $numeroOperation)) {
            // Rollback: delete all created operations
            foreach ($operations as $op) {
                $op->delete();
            }

            $totals = Operation::getOperationTotal($journalId, $numeroOperation);
            return response()->json([
                'error' => 'L\'opération n\'est pas équilibrée.',
                'debit' => $totals['debit'],
                'credit' => $totals['credit'],
            ], 422);
        }

        return response()->json([
            'message' => 'Opérations créées avec succès.',
            'operations' => $operations,
        ], 201);
    }

    /**
     * Get all operations for a specific operation number
     */
    public function getByOperationNumber(Request $request, string $numeroOperation)
    {
        $operations = Operation::whereHas('journal', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('numero_operation', $numeroOperation)->get();

        if ($operations->isEmpty()) {
            return response()->json(['message' => 'Aucune opération trouvée.'], 404);
        }

        $totals = Operation::getOperationTotal($operations->first()->journal_id, $numeroOperation);

        return response()->json([
            'numero_operation' => $numeroOperation,
            'operations' => $operations,
            'totals' => $totals,
            'is_balanced' => Operation::isBalanced($operations->first()->journal_id, $numeroOperation),
        ]);
    }

    /**
     * Get the next available operation number for a journal
     */
    public function getNextOperationNumber(Journal $journal)
    {
        $this->authorize('view', $journal);

        $nextNumber = Operation::getNextOperationNumber($journal->id);

        return response()->json([
            'journal_id' => $journal->id,
            'next_operation_number' => $nextNumber,
        ]);
    }

    /**
     * Validate if an operation (by numero_operation) is balanced
     */
    public function validateOperationBalance(Request $request, string $numeroOperation)
    {
        $operation = Operation::whereHas('journal', function ($query) {
            $query->where('user_id', Auth::id());
        })->where('numero_operation', $numeroOperation)->first();

        if (!$operation) {
            return response()->json(['message' => 'Opération non trouvée.'], 404);
        }

        $isBalanced = Operation::isBalanced($operation->journal_id, $numeroOperation);
        $totals = Operation::getOperationTotal($operation->journal_id, $numeroOperation);

        return response()->json([
            'numero_operation' => $numeroOperation,
            'is_balanced' => $isBalanced,
            'total_debit' => $totals['debit'],
            'total_credit' => $totals['credit'],
            'difference' => bcsub($totals['debit'], $totals['credit'], 2),
        ]);
    }
}
