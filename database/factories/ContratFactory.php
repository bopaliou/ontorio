<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Contrat>
 */
class ContratFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bien_id' => $this->faker->randomElement([\App\Models\Bien::factory()]),
            'locataire_id' => \App\Models\Locataire::factory(),
            'date_debut' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'date_fin' => $this->faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
            'loyer_montant' => $this->faker->numberBetween(50000, 500000),
            'statut' => $this->faker->randomElement(['actif', 'en_attente', 'résilié', 'expiré']),
            'caution' => $this->faker->numberBetween(50000, 200000),
            'frais_dossier' => $this->faker->numberBetween(10000, 50000),
            'type_bail' => $this->faker->randomElement(['habitation', 'commercial', 'professionnel', 'mixte']),
            'date_signature' => $this->faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'renouvellement_auto' => $this->faker->boolean(),
            'preavis_mois' => $this->faker->numberBetween(1, 12),
        ];
    }
}
