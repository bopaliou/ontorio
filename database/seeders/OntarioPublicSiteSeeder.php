<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Proprietaire;
use Illuminate\Database\Seeder;

/**
 * Seeder basé sur les annonces du site public Ontario Group
 * https://ontariogroup.net/annonces-immobilieres/
 *
 * Ces données représentent des biens typiques commercialisés par Ontario Group
 * dans les quartiers prisés de Dakar et ses environs.
 */
class OntarioPublicSiteSeeder extends Seeder
{
    public function run(): void
    {
        // Récupérer ou créer le propriétaire Ontario Group
        $ontario = Proprietaire::firstOrCreate(
            ['email' => 'commercial@ontariogroup.net'],
            [
                'nom' => 'ONTARIO GROUP',
                'telephone' => '+221 33 822 32 67 / 33 842 05 80 / 78 105 35 54',
                'adresse' => '5 Felix Gaure x Colbert Dakar, Dakar Plateau BP: 06813',
            ]
        );

        // Liste des biens basée sur le site public
        $biensData = [
            // === VILLAS ===
            [
                'nom' => 'Villa à louer à Ouakam',
                'adresse' => 'Ouakam, Dakar',
                'ville' => 'Dakar',
                'type' => 'villa',
                'surface' => 350.0,
                'nombre_pieces' => 5,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 2500000,
                'description' => 'Magnifique villa haut standing avec piscine et jardin paysager à Ouakam.',
            ],
            [
                'nom' => 'Villa de standing aux Almadies',
                'adresse' => 'Route des Almadies',
                'ville' => 'Dakar',
                'type' => 'villa',
                'surface' => 400.0,
                'nombre_pieces' => 6,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 3500000,
                'description' => 'Villa luxueuse avec vue mer, piscine à débordement et personnel de maison.',
            ],
            [
                'nom' => 'Villa Ngor Virage',
                'adresse' => 'Ngor, Virage',
                'ville' => 'Dakar',
                'type' => 'villa',
                'surface' => 280.0,
                'nombre_pieces' => 4,
                'meuble' => true,
                'statut' => 'libre',
                'loyer_mensuel' => 1800000,
                'description' => 'Belle villa meublée proche de la plage de Ngor.',
            ],
            [
                'nom' => 'Villa Fann Résidence',
                'adresse' => 'Fann Résidence',
                'ville' => 'Dakar',
                'type' => 'villa',
                'surface' => 320.0,
                'nombre_pieces' => 5,
                'meuble' => false,
                'statut' => 'occupé',
                'loyer_mensuel' => 2200000,
                'description' => 'Villa dans quartier résidentiel calme, proche ambassades.',
            ],
            [
                'nom' => 'Villa Mermoz Pyrotechnie',
                'adresse' => 'Mermoz, Pyrotechnie',
                'ville' => 'Dakar',
                'type' => 'villa',
                'surface' => 300.0,
                'nombre_pieces' => 5,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 1500000,
                'description' => 'Villa spacieuse avec terrasse et garage double.',
            ],

            // === APPARTEMENTS ===
            [
                'nom' => 'Appartement F4 Plateau',
                'adresse' => 'Avenue Albert Sarraut',
                'ville' => 'Dakar',
                'type' => 'appartement',
                'surface' => 120.0,
                'nombre_pieces' => 4,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 450000,
                'description' => 'Bel appartement au cœur du Plateau, vue sur la mer.',
            ],
            [
                'nom' => 'Appartement F3 Sacré-Cœur',
                'adresse' => 'Sacré-Cœur 3',
                'ville' => 'Dakar',
                'type' => 'appartement',
                'surface' => 95.0,
                'nombre_pieces' => 3,
                'meuble' => true,
                'statut' => 'occupé',
                'loyer_mensuel' => 350000,
                'description' => 'Appartement moderne entièrement meublé et équipé.',
            ],
            [
                'nom' => 'Appartement F2 Point E',
                'adresse' => 'Point E, Rue 3',
                'ville' => 'Dakar',
                'type' => 'appartement',
                'surface' => 70.0,
                'nombre_pieces' => 2,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 250000,
                'description' => 'Appartement fonctionnel dans résidence sécurisée.',
            ],
            [
                'nom' => 'Appartement Hann Maristes',
                'adresse' => 'Hann Maristes',
                'ville' => 'Dakar',
                'type' => 'appartement',
                'surface' => 110.0,
                'nombre_pieces' => 4,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 300000,
                'description' => 'Grand appartement familial avec parking.',
            ],
            [
                'nom' => 'Appartement Liberté 6',
                'adresse' => 'Liberté 6 extension',
                'ville' => 'Dakar',
                'type' => 'appartement',
                'surface' => 85.0,
                'nombre_pieces' => 3,
                'meuble' => false,
                'statut' => 'occupé',
                'loyer_mensuel' => 220000,
                'description' => 'Appartement lumineux proche commerces et transports.',
            ],

            // === STUDIOS ===
            [
                'nom' => 'Studio meublé Mamelles',
                'adresse' => 'Mamelles Ouakam',
                'ville' => 'Dakar',
                'type' => 'studio',
                'surface' => 35.0,
                'nombre_pieces' => 1,
                'meuble' => true,
                'statut' => 'libre',
                'loyer_mensuel' => 180000,
                'description' => 'Studio moderne tout équipé avec balcon vue mer.',
            ],
            [
                'nom' => 'Studio Fann Hock',
                'adresse' => 'Fann Hock',
                'ville' => 'Dakar',
                'type' => 'studio',
                'surface' => 40.0,
                'nombre_pieces' => 1,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 120000,
                'description' => 'Studio idéal pour étudiant ou jeune professionnel.',
            ],

            // === BUREAUX ===
            [
                'nom' => 'Bureau Open Space Point E',
                'adresse' => 'Point E, Avenue Cheikh Anta Diop',
                'ville' => 'Dakar',
                'type' => 'bureau',
                'surface' => 200.0,
                'nombre_pieces' => 0,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 800000,
                'description' => 'Plateau bureau moderne avec climatisation centrale.',
            ],
            [
                'nom' => 'Bureau Plateau Business',
                'adresse' => 'Place de l\'Indépendance',
                'ville' => 'Dakar',
                'type' => 'bureau',
                'surface' => 150.0,
                'nombre_pieces' => 0,
                'meuble' => true,
                'statut' => 'libre',
                'loyer_mensuel' => 1200000,
                'description' => 'Bureau prestige en plein centre des affaires.',
            ],

            // === MAGASINS ===
            [
                'nom' => 'Boutique Zone Industrielle',
                'adresse' => 'Zone Industrielle de Dakar',
                'ville' => 'Dakar',
                'type' => 'magasin',
                'surface' => 80.0,
                'nombre_pieces' => 0,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 350000,
                'description' => 'Local commercial avec vitrine et arrière-boutique.',
            ],

            // === DIAMNIADIO (Pôle urbain) ===
            [
                'nom' => 'Villa Diamniadio Lac Rose',
                'adresse' => 'Cité du Lac Rose, Diamniadio',
                'ville' => 'Diamniadio',
                'type' => 'villa',
                'surface' => 220.0,
                'nombre_pieces' => 4,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 600000,
                'description' => 'Villa neuve dans le nouveau pôle urbain de Diamniadio.',
            ],
            [
                'nom' => 'Appartement Résidence Diamniadio',
                'adresse' => 'Cité Ministères Diamniadio',
                'ville' => 'Diamniadio',
                'type' => 'appartement',
                'surface' => 100.0,
                'nombre_pieces' => 3,
                'meuble' => false,
                'statut' => 'libre',
                'loyer_mensuel' => 250000,
                'description' => 'Appartement moderne dans résidence sécurisée.',
            ],

            // === SALY (Zone touristique) ===
            [
                'nom' => 'Villa Saly Portudal',
                'adresse' => 'Saly Portudal, front de mer',
                'ville' => 'Saly',
                'type' => 'villa',
                'surface' => 280.0,
                'nombre_pieces' => 4,
                'meuble' => true,
                'statut' => 'libre',
                'loyer_mensuel' => 1200000,
                'description' => 'Villa de vacances avec piscine privée et accès plage.',
            ],
        ];

        foreach ($biensData as $bienData) {
            Bien::firstOrCreate(
                ['nom' => $bienData['nom']],
                array_merge($bienData, ['proprietaire_id' => $ontario->id])
            );
        }

        $this->command->info('✅ '.count($biensData).' biens du site public Ontario Group ont été créés.');
    }
}
