<?php

use App\Models\User;
use App\Models\Journal;
use App\Models\Operation;

// Get first user
$user = User::first();
if (!$user) {
    echo "Aucun utilisateur trouvé\n";
    exit;
}

// Create test journal
$journal = $user->journals()->create(['designation' => 'Journal Test Suppression']);
echo "Journal créé: ID={$journal->id}, Nom={$journal->designation}\n";

// Add operations
Operation::insert([
    [
        'journal_id' => $journal->id,
        'numero_operation' => 1,
        'date' => '2026-02-06',
        'reference' => 'TEST1',
        'libelle' => 'Test ligne 1',
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
        'reference' => 'TEST1',
        'libelle' => 'Test ligne 2',
        'debit' => null,
        'credit' => 1000,
        'numero_compte_general' => '701',
        'created_at' => now(),
        'updated_at' => now()
    ]
]);

echo "Opérations créées: " . $journal->operations()->count() . "\n";
echo "User ID du journal: {$journal->user_id}\n";
echo "User ID connecté: {$user->id}\n";
echo "Peut supprimer? " . ($journal->user_id === $user->id ? 'OUI' : 'NON') . "\n";
echo "\nJournal ID pour test: {$journal->id}\n";
