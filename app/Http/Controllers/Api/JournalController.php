<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreJournalRequest;
use App\Http\Requests\UpdateJournalRequest;
use App\Models\Journal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JournalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Auth::user()->journals);
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
    public function store(StoreJournalRequest $request)
    {
        $journal = Auth::user()->journals()->create($request->validated());

        return response()->json($journal, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Journal $journal)
    {
        $this->authorize('view', $journal);

        $journal->load('operations');
        $totals = $journal->getTotals();
        $isBalanced = $journal->isBalanced();

        return response()->json([
            'journal' => $journal,
            'totals' => $totals,
            'is_balanced' => $isBalanced,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Journal $journal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJournalRequest $request, Journal $journal)
    {
        $this->authorize('update', $journal);

        $journal->update($request->validated());

        return response()->json($journal);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Journal $journal)
    {
        $this->authorize('delete', $journal);

        $journal->delete();

        return response()->json(['message' => 'Journal deleted']);
    }

    /**
     * Get operations for a journal, grouped by numero_operation
     */
    public function getOperations(Journal $journal)
    {
        $this->authorize('view', $journal);

        $groupedOperations = $journal->getOperationsGrouped();

        return response()->json([
            'journal_id' => $journal->id,
            'journal_designation' => $journal->designation,
            'operations' => $groupedOperations,
        ]);
    }

    /**
     * Get totals (debit/credit) for a journal
     */
    public function getTotals(Journal $journal)
    {
        $this->authorize('view', $journal);

        $totals = $journal->getTotals();

        return response()->json($totals);
    }

    /**
     * Validate if a journal is balanced
     */
    public function validateBalance(Journal $journal)
    {
        $this->authorize('view', $journal);

        $isBalanced = $journal->isBalanced();
        $totals = $journal->getTotals();

        return response()->json([
            'is_balanced' => $isBalanced,
            'total_debit' => $totals['total_debit'],
            'total_credit' => $totals['total_credit'],
            'difference' => bcsub($totals['total_debit'], $totals['total_credit'], 2),
        ]);
    }
}
