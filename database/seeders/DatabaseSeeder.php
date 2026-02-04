<?php

namespace Database\Seeders;

use App\Models\Journal;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use function Laravel\Prompts\password;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);

        $journalist = Journal::factory(20)->create([
            'user_id' => fn () => $user->random()->id
        ]);

        $operation = Operation::factory(80)->create([
            'user_id' => fn () => $user->random()->id,
            'journal_id' => fn () => $journalist->random()->id
        ]);

    }
}
