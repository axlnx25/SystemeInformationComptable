<?php

use App\Models\Journal;
use App\Models\Operation;
use App\Models\User;

$user = User::first();
if (!$user) {
    echo "Aucun utilisateur\n";
    exit;
}

// Créer un journal de test
$journal = $user->journals()->create(['designation' => 'Test Suppression Backend']);
echo "Journal créé ID: {$journal->id}\n";

// Ajouter des opérations
Operation::insert([
    [
        'journal_id' => $journal->id,
        'numero_operation' => 1,
        'date' => '2026-02-06',
        'reference' => 'TEST',
        'libelle' => 'Test 1',
        'debit' => 1000,
        'credit' => null,
        'numero_compte_general' => '512',
        'created_at' => now(),
        'updated_at' => now()
    ],
    [
        'journal_id' => $journal->id,
        'numero_operation' => 1,
        'date' => '2026-02-06',
        'reference' => 'TEST',
        'libelle' => 'Test 2',
        'debit' => null,
        'credit' => 1000,
        'numero_compte_general' => '701',
        'created_at' => now(),
        'updated_at' => now()
    ]
]);

echo "Opérations créées: " . $journal->operations()->count() . "\n";

// Test suppression journal
echo "\n=== Test suppression journal ===\n";
$journalId = $journal->id;
$journal->delete();
echo "Journal supprimé\n";
echo "Opérations restantes: " . Operation::where('journal_id', $journalId)->count() . "\n";
