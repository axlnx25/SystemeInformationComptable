<?php

namespace Tests\Feature;

use App\Models\Journal;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OperationDeletionTest extends TestCase
{
    use RefreshDatabase;

    #[\PHPUnit\Framework\Attributes\Test]
    public function user_can_delete_an_operation_group_by_numero_operation(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Operation::factory()->count(2)->create([
            'journal_id' => $journal->id,
            'numero_operation' => 99,
        ]);

        Operation::factory()->create([
            'journal_id' => $journal->id,
            'numero_operation' => 100,
        ]);

        $this->assertDatabaseCount('operations', 3);

        $response = $this->delete(route('operations.destroy', [
            'journal' => $journal->id,
            'numeroOperation' => 99,
        ]));

        $response->assertRedirect(route('journals.history', $journal));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('operations', [
            'journal_id' => $journal->id,
            'numero_operation' => 99,
        ]);

        $this->assertDatabaseHas('operations', [
            'journal_id' => $journal->id,
            'numero_operation' => 100,
        ]);
    }
}

