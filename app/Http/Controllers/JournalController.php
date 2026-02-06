<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    /**
     * Display a listing of journals
     */
    public function index()
    {
        $journals = Auth::user()->journals()->withCount('operations')->get();
        
        // Calculate totals for each journal
        $journals->each(function ($journal) {
            $totals = $journal->getTotals();
            $journal->total_debit = $totals['total_debit'];
            $journal->total_credit = $totals['total_credit'];
            $journal->is_balanced = $journal->isBalanced();
        });
        
        return view('journals.index', compact('journals'));
    }

    /**
     * Show the form for creating a new journal
     */
    public function create()
    {
        return view('journals.create');
    }

    /**
     * Store a newly created journal
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $journal = Auth::user()->journals()->create($validated);

        return redirect()->route('journals.index')
            ->with('success', 'Journal créé avec succès !');
    }

    /**
     * Display the specified journal
     */
    public function show(Journal $journal)
    {
        $this->authorize('view', $journal);

        $journal->load('operations');
        $totals = $journal->getTotals();

        return view('journals.show', compact('journal', 'totals'));
    }

    /**
     * Show the form for editing the specified journal
     */
    public function edit(Journal $journal)
    {
        $this->authorize('update', $journal);

        return view('journals.edit', compact('journal'));
    }

    /**
     * Update the specified journal
     */
    public function update(Request $request, Journal $journal)
    {
        $this->authorize('update', $journal);

        $validated = $request->validate([
            'designation' => 'required|string|max:255',
        ]);

        $journal->update($validated);

        return redirect()->route('journals.index')
            ->with('success', 'Journal mis à jour avec succès !');
    }

    /**
     * Remove the specified journal
     */
    public function destroy(Journal $journal)
    {
        $this->authorize('delete', $journal);

        $journal->delete();

        return redirect()->route('journals.index')
            ->with('success', 'Journal supprimé avec succès !');
    }

    /**
     * Show operations entry form for a journal
     */
    public function operations(Journal $journal)
    {
        $this->authorize('view', $journal);

        // Get next operation number
        $nextOperationNumber = \App\Models\Operation::getNextOperationNumber($journal->id);

        return view('journals.operations', compact('journal', 'nextOperationNumber'));
    }

    /**
     * Show history of operations for a journal
     */
    public function history(Journal $journal)
    {
        $this->authorize('view', $journal);

        $groupedOperations = $journal->getOperationsGrouped();
        $totals = $journal->getTotals();

        return view('journals.history', compact('journal', 'groupedOperations', 'totals'));
    }

    /**
     * Show form for creating a new journal with operations
     */
    public function newJournal()
    {
        return view('journals.new');
    }

    /**
     * Save journal with operations
     */
    public function saveJournalWithOperations(Request $request)
    {
        $request->validate([
            'journal_name' => 'required|string|max:255',
            'operations' => 'required|array|min:2',
            'operations.*.date' => 'required|date',
            'operations.*.numero_operation' => 'required|string|max:255',
            'operations.*.reference' => 'nullable|string|max:255',
            'operations.*.libelle' => 'required|string|max:255',
            'operations.*.debit' => 'nullable|numeric|min:0',
            'operations.*.credit' => 'nullable|numeric|min:0',
            'operations.*.numero_compte_general' => 'required|string|max:255',
        ]);

        // Create journal
        $journal = Auth::user()->journals()->create([
            'designation' => $request->journal_name
        ]);

        // Prepare operations
        $operations = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($request->operations as $lineData) {
            $debit = floatval($lineData['debit'] ?? 0);
            $credit = floatval($lineData['credit'] ?? 0);

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
            $journal->delete(); // Rollback journal creation
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => sprintf('Journal non équilibré (Débit %.2f / Crédit %.2f).', $totalDebit, $totalCredit)
                ], 422);
            }
            
            return back()->withErrors(['balance' => 'Le journal n\'est pas équilibré.'])->withInput();
        }

        // Insert operations
        \App\Models\Operation::insert($operations);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Journal enregistré avec succès !',
                'journal_id' => $journal->id
            ]);
        }

        return redirect()->route('journals.index')
            ->with('success', 'Journal créé avec succès !');
    }
}
