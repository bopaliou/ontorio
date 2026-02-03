<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Proprietaire;
use App\Models\Bien;
use App\Models\Locataire;
use App\Models\Contrat;
use App\Models\Loyer;
use App\Models\Paiement;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        // Utilisateurs (Gérés par RoleUsersSeeder)
        /*
        User::firstOrCreate(
            ['email' => 'admin@ontariogroup.net'],
            ['name' => 'Admin Ontario', 'role' => 'admin', 'password' => bcrypt('password')]
        );
        User::firstOrCreate(
            ['email' => 'gestionnaire@ontariogroup.net'],
            ['name' => 'Gestionnaire Ontario', 'role' => 'gestionnaire', 'password' => bcrypt('password')]
        );
        User::firstOrCreate(
            ['email' => 'comptable@ontariogroup.net'],
            ['name' => 'Comptable Ontario', 'role' => 'comptable', 'password' => bcrypt('password')]
        );
        */

        // LE BAILLEUR UNIQUE
        $ontario = Proprietaire::firstOrCreate(
            ['email' => 'commercial@ontariogroup.net'],
            [
                'nom' => 'ONTARIO GROUP', 
                'telephone' => '+221 33 822 32 67 / 33 842 05 80 / 78 105 35 54',
                'adresse' => '5 Felix Gaure x Colbert Dakar, Dakar Plateau BP: 06813'
            ]
        );

        // LES BIENS
        $biens = [
            Bien::firstOrCreate(
                ['nom' => 'Appartement Yoff A1'],
                [
                    'adresse' => 'Rue des Almadies',
                    'ville' => 'Dakar',
                    'type' => 'appartement',
                    'surface' => 80.0,
                    'statut' => 'occupé',
                    'loyer_mensuel' => 250000,
                    'proprietaire_id' => $ontario->id
                ]
            ),
            Bien::firstOrCreate(
                ['nom' => 'Villa Liberté 6'],
                [
                    'adresse' => 'Cité Keur Gorgui',
                    'ville' => 'Dakar',
                    'type' => 'villa',
                    'surface' => 250.0,
                    'statut' => 'libre',
                    'loyer_mensuel' => 650000,
                    'proprietaire_id' => $ontario->id
                ]
            ),
            Bien::firstOrCreate(
                ['nom' => 'Studio Plateau'],
                [
                    'adresse' => 'Avenue Pompidou',
                    'ville' => 'Dakar',
                    'type' => 'studio',
                    'surface' => 45.0,
                    'statut' => 'occupé',
                    'loyer_mensuel' => 180000,
                    'proprietaire_id' => $ontario->id
                ]
            ),
        ];

        // Locataires
        $locataires = [
            Locataire::firstOrCreate(
                ['email' => 'fatou.sarr@gmail.com'],
                ['nom' => 'Fatou Sarr', 'telephone' => '76 112 23 34']
            ),
            Locataire::firstOrCreate(
                ['email' => 'cheikh.ba@gmail.com'],
                ['nom' => 'Cheikh Ba', 'telephone' => '70 987 76 55']
            ),
        ];

        // Contrats
        $contrat1 = Contrat::firstOrCreate(
            ['bien_id' => $biens[0]->id, 'locataire_id' => $locataires[0]->id],
            [
                'date_debut' => '2025-01-01',
                'date_fin' => '2025-12-31',
                'loyer_montant' => 250000,
                'statut' => 'actif',
            ]
        );

        $contrat2 = Contrat::firstOrCreate(
            ['bien_id' => $biens[2]->id, 'locataire_id' => $locataires[1]->id],
            [
                'date_debut' => '2025-03-01',
                'date_fin' => '2026-02-28',
                'loyer_montant' => 180000,
                'statut' => 'actif',
            ]
        );

        $moisJanvier = '2026-01';

        // Loyers
        $loyer1 = Loyer::firstOrCreate(
            ['contrat_id' => $contrat1->id, 'mois' => $moisJanvier],
            ['montant' => 250000, 'statut' => 'payé']
        );
        $loyer2 = Loyer::firstOrCreate(
            ['contrat_id' => $contrat2->id, 'mois' => $moisJanvier],
            ['montant' => 180000, 'statut' => 'en_retard']
        );

        // Paiements
        Paiement::firstOrCreate(
            ['loyer_id' => $loyer1->id],
            [
                'date_paiement' => '2026-01-05',
                'montant' => 250000,
                'mode' => 'virement'
            ]
        );
    }
}
