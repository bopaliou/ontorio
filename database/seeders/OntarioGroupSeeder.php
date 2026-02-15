<?php

namespace Database\Seeders;

use App\Models\Proprietaire;
use Illuminate\Database\Seeder;

class OntarioGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Proprietaire::firstOrCreate(
            ['nom' => 'Ontario Group'],
            [
                'prenom' => 'S.A.',
                'email' => 'contact@ontariogroup.net',
                'telephone' => '33 822 32 67',
                'adresse' => '5 Félix Faure x Colbert, Dakar Plateau',
                // Add validation/status if needed
            ]
        );
    }
}
