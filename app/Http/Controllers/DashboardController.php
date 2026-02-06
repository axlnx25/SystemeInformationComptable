<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the dashboard
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get statistics
        $totalJournals = $user->journals()->count();
        $totalOperations = \App\Models\Operation::whereHas('journal', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->count();
        
        // Get recent journals
        $recentJournals = $user->journals()
            ->withCount('operations')
            ->latest()
            ->take(5)
            ->get();
        
        // Calculate totals for each journal
        $recentJournals->each(function ($journal) {
            $totals = $journal->getTotals();
            $journal->total_debit = $totals['total_debit'];
            $journal->total_credit = $totals['total_credit'];
            $journal->is_balanced = $journal->isBalanced();
        });
        
        return view('dashboard', compact('totalJournals', 'totalOperations', 'recentJournals'));
    }
}
