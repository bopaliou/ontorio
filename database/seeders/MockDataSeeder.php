<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Depense;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $faker = \Faker\Factory::create('fr_FR');

        // 1. UTILISATEURS (5 de chaque rôle important)
        $roles = ['gestionnaire', 'comptable', 'direction'];
        foreach ($roles as $role) {
            for ($i = 0; $i < 5; $i++) {
                User::firstOrCreate(
                    ['email' => strtolower($role) . ($i + 1) . '@ontariogroup.net'],
                    [
                        'name' => ucfirst($role) . ' ' . $faker->firstName,
                        'role' => $role,
                        'password' => bcrypt('password'),
                    ]
                );
            }
        }

        // 2. PROPRIÉTAIRES (5 Minimum + Ontario Group)
        $proprietaires = [];
        // Garder Ontario Group comme principal
        $proprietaires[] = Proprietaire::firstOrCreate(
            ['email' => 'commercial@ontariogroup.net'],
            [
                'nom' => 'ONTARIO GROUP',
                'telephone' => '+221 33 822 32 67',
                'adresse' => '5 Felix Faure x Colbert, Dakar',
            ]
        );

        for ($i = 0; $i < 5; $i++) {
            $proprietaires[] = Proprietaire::create([
                'nom' => $faker->company,
                'email' => $faker->unique()->companyEmail,
                'telephone' => $faker->phoneNumber,
                'adresse' => $faker->address,
            ]);
        }

        // 3. LOCATAIRES (5 Minimum)
        $locataires = [];
        for ($i = 0; $i < 5; $i++) {
            $locataires[] = Locataire::firstOrCreate(
                ['email' => $faker->unique()->email],
                [
                    'nom' => $faker->name,
                    'telephone' => $faker->phoneNumber,
                    'pieces_identite' => $faker->numerify('##############'),
                ]
            );
        }

        // 4. BIENS (5 Minimum)
        $biens = [];
        $types = ['appartement', 'villa', 'studio', 'bureau', 'magasin', 'entrepot'];

        // Distribuer les biens entre les propriétaires
        for ($i = 0; $i < 5; $i++) {
            $proprio = $proprietaires[$i % count($proprietaires)]; // Rotate owners
            $biens[] = Bien::create([
                'nom' => $types[array_rand($types)] . ' ' . $faker->citySuffix,
                'adresse' => $faker->streetAddress,
                'ville' => 'Dakar',
                'type' => $types[array_rand($types)],
                'surface' => $faker->numberBetween(25, 300),
                'statut' => 'libre', // Sera mis à jour par le contrat
                'loyer_mensuel' => $faker->numberBetween(100000, 1500000),
                'proprietaire_id' => $proprio->id,
            ]);
        }

        // 5. CONTRATS (5 Minimum)
        $contrats = [];
        for ($i = 0; $i < 5; $i++) {
            // Ensure we have enough biens and locataires, otherwise fallback or create new
            $bien = $biens[$i] ?? $biens[0];
            $locataire = $locataires[$i] ?? $locataires[0];

            $contrats[] = Contrat::create([
                'bien_id' => $bien->id,
                'locataire_id' => $locataire->id,
                'date_debut' => $faker->dateTimeBetween('-2 years', '-1 year')->format('Y-m-d'),
                'date_fin' => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d'),
                'loyer_montant' => $bien->loyer_mensuel,
                'statut' => 'actif',
            ]);

            // Mettre à jour le statut du bien
            $bien->update(['statut' => 'occupé']);
        }

        // 6. LOYERS & PAIEMENTS (Pour chaque contrat, générer historique récent)
        foreach ($contrats as $contrat) {
            // Générer loyer pour le mois en cours
            $mois = date('Y-m');

            $loyer = Loyer::firstOrCreate(
                ['contrat_id' => $contrat->id, 'mois' => $mois],
                [
                    'montant' => $contrat->loyer_montant,
                    'statut' => $faker->randomElement(['payé', 'payé', 'payé', 'en_retard', 'partiellement_payé']),
                ]
            );

            // Si payé ou partiel, créer un paiement
            if (in_array($loyer->statut, ['payé', 'partiellement_payé'])) {
                Paiement::create([
                    'loyer_id' => $loyer->id,
                    'date_paiement' => Carbon::parse($mois . '-01')->addDays(rand(0, 25)),
                    'montant' => $loyer->statut === 'payé' ? $loyer->montant : ($loyer->montant / 2),
                    'mode' => $faker->randomElement(['espèces', 'virement', 'chèque', 'mobile_money']),
                    'reference' => strtoupper($faker->bothify('REF-####-??')),
                    'user_id' => User::first()->id ?? 1,
                ]);
            }
        }

        // 7. DÉPENSES (Générer quelques dépenses pour le mois actuel)
        for ($i = 0; $i < 3; $i++) {
            Depense::create([
                'bien_id' => $biens[array_rand($biens)]->id,
                'titre' => $faker->randomElement(['Réparation fuite', 'Entretien climatisation', 'Électricité parties communes', 'Peinture couloir']),
                'montant' => $faker->numberBetween(5000, 50000),
                'date_depense' => Carbon::now()->subDays(rand(1, 20)),
                'categorie' => $faker->randomElement(['maintenance', 'travaux', 'taxe', 'assurance', 'autre']),
                'statut' => 'payé',
            ]);
        }
    }
}
