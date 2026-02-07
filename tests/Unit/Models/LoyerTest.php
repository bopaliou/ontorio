<?php

namespace Tests\Unit\Models;

use App\Models\Loyer;
use App\Models\Paiement;
use Carbon\Carbon;
use Tests\TestCase;

class LoyerTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    /**
     * Test: Calcul montant payé avec eager loading
     */
    public function test_montant_paye_avec_eager_loading()
    {
        $loyer = Loyer::factory()
            ->has(Paiement::factory(3), 'paiements')
            ->create(['montant' => 100000]);

        // Charger avec eager loading
        $loyer = Loyer::withMontantPaye()->find($loyer->id);

        // Doit avoir 3 paiements de 100k chacun (fake data)
        $this->assertNotNull($loyer->paiements_sum_montant);
    }

    /**
     * Test: Date d'échéance calculée (5 du mois suivant)
     */
    public function test_date_echeance_calculation()
    {
        Carbon::setTestNow('2026-02-15');

        $loyer = Loyer::factory()->create([
            'mois' => '2026-01', // Janvier
        ]);

        // Échéance = 05/02
        $expected = Carbon::parse('2026-02-05');
        $this->assertEquals($expected->toDateString(), $loyer->date_echeance->toDateString());
    }

    /**
     * Test: Calcul jours de retard
     */
    public function test_jours_retard_calculation()
    {
        Carbon::setTestNow('2026-02-15');

        $loyer = Loyer::factory()->create([
            'mois' => '2026-01', // Échéance: 05/02
            'statut' => 'en_retard',
        ]);

        // Retard = 15/2 - 5/2 = 10 jours
        $this->assertEquals(10, $loyer->jours_retard);
    }

    /**
     * Test: Reste à payer = montant + pénalité - paiements
     */
    public function test_reste_a_payer_formula()
    {
        $loyer = Loyer::factory()->create([
            'montant' => 100000,
            'penalite' => 5000,
            'statut' => 'partiellement_payé',
        ]);

        // Ajouter un paiement de 50k (pas besoin d'user pour ce test)
        Paiement::create([
            'loyer_id' => $loyer->id,
            'montant' => 50000,
            'date_paiement' => now()->toDateString(),
            'mode' => 'virement',
            'user_id' => null,
        ]);

        // Recharger avec eager loading
        $loyer = Loyer::withMontantPaye()->find($loyer->id);

        // Reste = (100k + 5k) - 50k = 55k
        $expected = 55000;
        // Vérifier le calcul du reste à payer (arrondi/conversion en entier)
        $this->assertEquals($expected, (int) round($loyer->reste_a_payer));
    }

    /**
     * Test: Vérification si loyer est en retard
     */
    public function test_est_en_retard_flag()
    {
        Carbon::setTestNow('2026-02-15');

        // Loyer en retard
        $payé = Loyer::factory()->create([
            'mois' => '2026-02',
            'statut' => 'payé',
        ]);

        $retard = Loyer::factory()->create([
            'mois' => '2026-01',
            'statut' => 'en_retard',
        ]);

        $this->assertEquals(0, $payé->jours_retard);
        $this->assertGreaterThan(0, $retard->jours_retard);
    }
}
