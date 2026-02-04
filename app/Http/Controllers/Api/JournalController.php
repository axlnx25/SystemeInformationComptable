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

        return response()->json($journal);
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
}
