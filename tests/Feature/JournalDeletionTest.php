<?php

namespace Tests\Feature;

use App\Models\Journal;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JournalDeletionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_delete_own_journal_and_its_operations(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'designation' => 'Journal Test Suppression',
        ]);

        // Crée deux lignes d'opération équilibrées
        Operation::factory()->create([
            'journal_id' => $journal->id,
            'numero_operation' => 1,
            'debit' => 1000,
            'credit' => null,
        ]);

        Operation::factory()->create([
            'journal_id' => $journal->id,
            'numero_operation' => 1,
            'debit' => null,
            'credit' => 1000,
        ]);

        $this->assertDatabaseCount('operations', 2);

        $response = $this->delete(route('journals.destroy', $journal));

        $response->assertRedirect(route('journals.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('journals', ['id' => $journal->id]);
        $this->assertDatabaseMissing('operations', ['journal_id' => $journal->id]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_cannot_delete_journal_of_another_user(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();

        $journal = Journal::factory()->create([
            'user_id' => $owner->id,
        ]);

        $this->actingAs($other);

        $response = $this->delete(route('journals.destroy', $journal));

        $response->assertStatus(403);
        $this->assertDatabaseHas('journals', ['id' => $journal->id]);
    }
}

