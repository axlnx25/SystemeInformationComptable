<?php

namespace Database\Factories;

use App\Models\Journal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Operation>
 */
class OperationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'date' => fake()->dateTime(),
            'reference' => fake()->randomNumber(),
            'libelle' => fake()->sentence(),
            'debit' => fake()->randomNumber(),
            'credit' => fake()->randomNumber(),
            'numero_compte_generale' => fake()->randomNumber(),
            'journal_id' => Journal::factory()
        ];
    }
}
