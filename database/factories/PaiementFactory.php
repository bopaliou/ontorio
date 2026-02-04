<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'loyer_id' => \App\Models\Loyer::factory(),
            'montant' => $this->faker->numberBetween(10000, 200000),
            'mode' => $this->faker->randomElement(['espèces', 'virement', 'chèque', 'mobile_money']),
            'date_paiement' => now()->format('Y-m-d'),
            'reference' => 'PAY-'.strtoupper(uniqid()),
        ];
    }
}
