<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Loyer>
 */
class LoyerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contrat_id' => \App\Models\Contrat::factory(),
            'mois' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-01'),
            'montant' => $this->faker->numberBetween(50000, 500000),
            'commission' => $this->faker->numberBetween(5000, 50000),
            'statut' => $this->faker->randomElement(['émis', 'en_retard', 'payé', 'annulé']),
            'penalite' => 0,
            'taux_penalite' => 10,
        ];
    }
}
