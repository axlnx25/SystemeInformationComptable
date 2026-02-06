<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OperationController extends Controller
{
    /**
     * Store batch operations
     */
    public function storeBatch(Request $request, Journal $journal)
    {
        $this->authorize('view', $journal);

        // Support both 'lines' and 'operations' parameter names
        $linesData = $request->input('lines', $request->input('operations', []));

        $request->merge(['lines' => $linesData]);

        $request->validate([
            'lines' => 'required|array|min:2',
            'lines.*.date' => 'required|date',
            'lines.*.numero_operation' => 'required|string|max:255',
            'lines.*.reference' => 'nullable|string|max:255',
            'lines.*.libelle' => 'required|string|max:255',
            'lines.*.debit' => 'nullable|numeric|min:0',
            'lines.*.credit' => 'nullable|numeric|min:0',
            'lines.*.numero_compte_general' => 'required|string|max:255',
        ]);

        $operations = [];
        $totalDebit = 0;
        $totalCredit = 0;
        $numeroOperation = null;

        // Validate and prepare operations
        foreach ($linesData as $lineData) {
            $debit = floatval($lineData['debit'] ?? 0);
            $credit = floatval($lineData['credit'] ?? 0);

            // Validate debit/credit exclusivity
            if ($debit > 0 && $credit > 0) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Une ligne ne peut pas avoir à la fois un débit et un crédit.'], 422);
                }
                return back()->withErrors(['lines' => 'Une ligne ne peut pas avoir à la fois un débit et un crédit.'])->withInput();
            }

            if ($debit == 0 && $credit == 0) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Une ligne doit avoir soit un débit, soit un crédit.'], 422);
                }
                return back()->withErrors(['lines' => 'Une ligne doit avoir soit un débit, soit un crédit.'])->withInput();
            }

            if ($numeroOperation === null) {
                $numeroOperation = $lineData['numero_operation'];
            }

            $totalDebit += $debit;
            $totalCredit += $credit;

            $operations[] = [
                'journal_id' => $journal->id,
                'numero_operation' => $lineData['numero_operation'],
                'date' => $lineData['date'],
                'reference' => $lineData['reference'] ?? null,
                'libelle' => $lineData['libelle'],
                'debit' => $debit > 0 ? $debit : null,
                'credit' => $credit > 0 ? $credit : null,
                'numero_compte_general' => $lineData['numero_compte_general'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Validate balance
        if (abs($totalDebit - $totalCredit) > 0.01) {
            $message = sprintf(
                'L\'opération n\'est pas équilibrée. Débit: %.2f, Crédit: %.2f, Différence: %.2f',
                $totalDebit,
                $totalCredit,
                abs($totalDebit - $totalCredit)
            );
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            
            return back()->withErrors(['balance' => $message])->withInput();
        }

        // Insert all operations
        DB::beginTransaction();
        try {
            Operation::insert($operations);
            DB::commit();

            $successMessage = sprintf('Opération n°%s enregistrée avec succès ! (%d lignes)', $numeroOperation, count($operations));
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'numero_operation' => $numeroOperation,
                    'lines_count' => count($operations)
                ]);
            }

            return redirect()->route('journals.history', $journal)
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()], 500);
            }
            
            return back()->withErrors(['error' => 'Erreur lors de l\'enregistrement : ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete an operation (all lines with same numero_operation)
     */
    public function destroy(Journal $journal, $numeroOperation)
    {
        $this->authorize('view', $journal);

        $deleted = Operation::where('journal_id', $journal->id)
            ->where('numero_operation', $numeroOperation)
            ->delete();

        return redirect()->route('journals.history', $journal)
            ->with('success', sprintf('Opération n°%s supprimée (%d lignes).', $numeroOperation, $deleted));
    }
}
