<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Bien>
 */
class BienFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'proprietaire_id' => \App\Models\Proprietaire::factory(),
            'nom' => $this->faker->word().' Residence',
            'adresse' => $this->faker->address(),
            'ville' => 'Dakar',
            'loyer_mensuel' => $this->faker->numberBetween(50000, 500000),
            'type' => $this->faker->randomElement(['appartement', 'studio', 'magasin', 'bureau']),
            'surface' => $this->faker->numberBetween(20, 300),
            'nombre_pieces' => $this->faker->numberBetween(1, 5),
            'meuble' => $this->faker->boolean(),
        ];
    }
}
