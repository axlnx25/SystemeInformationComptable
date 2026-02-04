<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOperationRequest;
use App\Http\Requests\UpdateOperationRequest;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OperationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $operations = Operation::whereHas('journal', function ($query) {
            $query->where('user_id', Auth::id());
        })->get();

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
        $operation = Operation::create($request->validated());

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
}
