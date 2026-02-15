<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Locataire;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\Proprietaire;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SenegalDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. PROPRIÉTAIRES (5)
        $proprietaires = [
            ['nom' => 'Ontario Group S.A.', 'email' => 'contact@ontariogroup.sn', 'telephone' => '+221 33 822 00 00', 'adresse' => 'Plateau, Dakar'],
            ['nom' => 'Moussa Diop', 'email' => 'm.diop@example.com', 'telephone' => '+221 77 634 12 34', 'adresse' => 'Almadies, Dakar'],
            ['nom' => 'Aminata Ndiaye', 'email' => 'a.ndiaye@example.com', 'telephone' => '+221 78 456 78 90', 'adresse' => 'Mermoz, Dakar'],
            ['nom' => 'Ibrahima Fall', 'email' => 'i.fall@example.com', 'telephone' => '+221 70 123 45 67', 'adresse' => 'Sacré-Cœur, Dakar'],
            ['nom' => 'Fatou Gueye', 'email' => 'f.gueye@example.com', 'telephone' => '+221 76 987 65 43', 'adresse' => 'Hann Mariste, Dakar'],
        ];

        foreach ($proprietaires as $p) {
            Proprietaire::updateOrCreate(['email' => $p['email']], $p);
        }

        $allProps = Proprietaire::all();

        // 2. BIENS (5+)
        $biensData = [
            ['nom' => 'Résidence Horizon', 'type' => 'appartement', 'ville' => 'Dakar', 'adresse' => 'Almadies, Zone 1', 'loyer_mensuel' => 450000],
            ['nom' => 'Villa Sable d\'Or', 'type' => 'villa', 'ville' => 'Dakar', 'adresse' => 'Ngor Virage', 'loyer_mensuel' => 1200000],
            ['nom' => 'Immeuble Le Plateau', 'type' => 'bureau', 'ville' => 'Dakar', 'adresse' => 'Avenue Roume, Plateau', 'loyer_mensuel' => 850000],
            ['nom' => 'Studio Moderne Mermoz', 'type' => 'studio', 'ville' => 'Dakar', 'adresse' => 'Mermoz Pyrotechnie', 'loyer_mensuel' => 250000],
            ['nom' => 'Local Commercial Ouakam', 'type' => 'magasin', 'ville' => 'Dakar', 'adresse' => 'Route de la Corniche, Ouakam', 'loyer_mensuel' => 600000],
        ];

        foreach ($biensData as $index => $b) {
            Bien::updateOrCreate(
                ['nom' => $b['nom']],
                array_merge($b, [
                    'proprietaire_id' => $allProps[$index % 5]->id,
                    'surface' => rand(40, 300),
                    'nombre_pieces' => rand(1, 6),
                    'statut' => 'libre',
                ])
            );
        }

        $allBiens = Bien::all();

        // 3. LOCATAIRES (5)
        $locatairesData = [
            ['nom' => 'Oumar Sow', 'email' => 'o.sow@client.sn', 'telephone' => '+221 77 111 22 33', 'adresse' => 'Médina, Dakar', 'cni' => '1234567890123'],
            ['nom' => 'Mariama Diallo', 'email' => 'm.diallo@client.sn', 'telephone' => '+221 78 222 33 44', 'adresse' => 'Liberté 6, Dakar', 'cni' => '2345678901234'],
            ['nom' => 'Abdoulaye Ba', 'email' => 'a.ba@client.sn', 'telephone' => '+221 70 333 44 55', 'adresse' => 'Parcelles Assainies, Dakar', 'cni' => '3456789012345'],
            ['nom' => 'Awa Sy', 'email' => 'a.sy@client.sn', 'telephone' => '+221 76 444 55 66', 'adresse' => 'Guédiawaye, Dakar', 'cni' => '4567890123456'],
            ['nom' => 'Cheikh Wade', 'email' => 'c.wade@client.sn', 'telephone' => '+221 77 555 66 77', 'adresse' => 'Pikine, Dakar', 'cni' => '5678901234567'],
        ];

        foreach ($locatairesData as $l) {
            Locataire::updateOrCreate(['email' => $l['email']], $l);
        }

        $allLocataires = Locataire::all();

        // 4. CONTRATS (5)
        for ($i = 0; $i < 5; $i++) {
            $bien = $allBiens[$i];
            $locataire = $allLocataires[$i];

            $contrat = Contrat::updateOrCreate(
                ['bien_id' => $bien->id, 'locataire_id' => $locataire->id],
                [
                    'date_debut' => Carbon::now()->subMonths(rand(1, 12))->startOfMonth(),
                    'loyer_montant' => $bien->loyer_mensuel,
                    'statut' => 'actif',
                    'caution' => $bien->loyer_mensuel * 2,
                    'frais_dossier' => 25000,
                    'type_bail' => 'habitation',
                    'date_signature' => Carbon::now()->subMonths(13),
                ]
            );

            // Mettre à jour le statut du bien
            $bien->update(['statut' => 'occupé']);

            // 5. LOYERS & PAIEMENTS pour chaque contrat
            $startDate = Carbon::parse($contrat->date_debut);
            $endDate = Carbon::now()->startOfMonth();

            while ($startDate->lte($endDate)) {
                $loyer = Loyer::updateOrCreate(
                    ['contrat_id' => $contrat->id, 'mois' => $startDate->format('Y-m')],
                    [
                        'montant' => $contrat->loyer_montant,
                        'statut' => 'émis',
                    ]
                );

                // Simuler des paiements pour les mois passés
                if ($startDate->lt($endDate) || rand(0, 1)) {
                    Paiement::create([
                        'loyer_id' => $loyer->id,
                        'montant' => $loyer->montant,
                        'mode' => 'espèces',
                        'date_paiement' => $startDate->copy()->day(5),
                        'reference' => 'PAY-'.strtoupper(Str::random(8)),
                    ]);
                    $loyer->update(['statut' => 'payé']);
                }

                $startDate->addMonth();
            }
        }

        // 6. DÉPENSES (5)
        $categories = ['maintenance', 'travaux', 'taxe', 'assurance', 'autre'];
        for ($i = 0; $i < 5; $i++) {
            $cat = $categories[rand(0, 4)];
            Depense::create([
                'bien_id' => $allBiens[rand(0, 4)]->id,
                'titre' => 'Intervention '.ucfirst($cat),
                'montant' => rand(5000, 150000),
                'date_depense' => Carbon::now()->subDays(rand(1, 30)),
                'categorie' => $cat,
                'description' => 'Facture électricien/plombier suite appel locataire.',
                'statut' => 'payé',
            ]);
        }

        // 7. IMAGES (Récupération des images existantes dans storage)
        $imageFiles = [];
        $bienStoragePath = storage_path('app/public/biens');
        
        if (file_exists($bienStoragePath)) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($bienStoragePath));
            $basePath = str_replace('\\', '/', storage_path('app/public/'));
            
            foreach ($files as $file) {
                if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png'])) {
                    $fullPath = str_replace('\\', '/', $file->getRealPath());
                    $relativePath = str_replace($basePath, '', $fullPath);
                    $imageFiles[] = ltrim($relativePath, '/');
                }
            }
        }

        if (!empty($imageFiles)) {
            foreach ($allBiens as $index => $bien) {
                if (isset($imageFiles[$index])) {
                    \App\Models\BienImage::updateOrCreate(
                        ['bien_id' => $bien->id, 'chemin' => $imageFiles[$index]],
                        [
                            'nom_original' => basename($imageFiles[$index]),
                            'principale' => true,
                            'ordre' => 0
                        ]
                    );
                }
            }
        }
    }
}
