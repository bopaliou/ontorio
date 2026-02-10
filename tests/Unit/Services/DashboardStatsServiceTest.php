<?php

namespace Tests\Unit\Services;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Loyer;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Services\DashboardStatsService;
use Carbon\Carbon;
use Tests\TestCase;

class DashboardStatsServiceTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    private const TEST_MONTH = '2026-02';

    private DashboardStatsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DashboardStatsService;
        Carbon::setTestNow('2026-02-15');
    }

    /**
     * Test: Calcul des loyers facturés pour un mois
     */
    public function test_financial_kpis_loyers_factures()
    {
        // Setup: créer un propriétaire, bien, locataire et contrat
        $proprio = Proprietaire::factory()->create();
        $bien = Bien::factory()->create(['proprietaire_id' => $proprio->id, 'loyer_mensuel' => 100000]);
        $locataire = Locataire::factory()->create();

        $contrat = Contrat::factory()->create([
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'loyer_montant' => 100000,
            'statut' => 'actif',
        ]);

        // Créer un loyer pour février
        Loyer::create([
            'contrat_id' => $contrat->id,
            'mois' => self::TEST_MONTH,
            'montant' => 100000,
            'statut' => 'émis',
        ]);

        // Test
        $kpis = $this->service->getFinancialKPIs(self::TEST_MONTH);

        $this->assertEquals(100000, $kpis['loyers_factures']);
        $this->assertArrayHasKey('nb_loyers', $kpis);
        $this->assertEquals(1, $kpis['nb_loyers']);
    }

    /**
     * Test: Calcul du taux de recouvrement
     */
    public function test_financial_kpis_taux_recouvrement()
    {
        $proprio = Proprietaire::factory()->create();
        $bien = Bien::factory()->create(['proprietaire_id' => $proprio->id]);
        $locataire = Locataire::factory()->create();

        $contrat = Contrat::factory()->create([
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'loyer_montant' => 100000,
        ]);

        // Loyer de 100k facturé
        $loyer = Loyer::create([
            'contrat_id' => $contrat->id,
            'mois' => self::TEST_MONTH,
            'montant' => 100000,
            'statut' => 'payé',
        ]);

        // Paiement de 50k (50% du loyer)
        // Paiement sans user (utile pour calcul des KPI)
        Paiement::create([
            'loyer_id' => $loyer->id,
            'montant' => 50000,
            'date_paiement' => now()->toDateString(),
            'mode' => 'virement',
            'user_id' => null,
        ]);

        $kpis = $this->service->getFinancialKPIs(self::TEST_MONTH);

        // Taux = (50000 / 100000) * 100 = 50%
        $this->assertEquals(50.0, $kpis['taux_recouvrement']);
    }

    /**
     * Test: Calcul des arriérés total
     */
    public function test_financial_kpis_arrieres()
    {
        $proprio = Proprietaire::factory()->create();
        $bien = Bien::factory()->create(['proprietaire_id' => $proprio->id]);
        $locataire = Locataire::factory()->create();

        $contrat = Contrat::factory()->create([
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'loyer_montant' => 100000,
        ]);

        // Loyer impayé
        Loyer::create([
            'contrat_id' => $contrat->id,
            'mois' => '2026-01',
            'montant' => 100000,
            'statut' => 'en_retard',
        ]);

        $kpis = $this->service->getFinancialKPIs(self::TEST_MONTH);

        // Doit inclure le loyer en retard du mois précédent
        $this->assertGreaterThan(0, $kpis['arrieres_total']);
    }

    /**
     * Test: Statistiques parc immobilier
     */
    public function test_parc_stats_occupancy_rate()
    {
        $proprio = Proprietaire::factory()->create();

        // 2 biens
        $bien1 = Bien::factory()->create(['proprietaire_id' => $proprio->id]);
        Bien::factory()->create(['proprietaire_id' => $proprio->id]);

        $locataire = Locataire::factory()->create();

        // 1 contrat actif = 1 bien occupé
        Contrat::factory()->create([
            'bien_id' => $bien1->id,
            'locataire_id' => $locataire->id,
            'statut' => 'actif',
        ]);

        $stats = $this->service->getParcStats();

        $this->assertEquals(2, $stats['total_biens']);
        $this->assertEquals(1, $stats['biens_occupes']);
        $this->assertEquals(1, $stats['biens_vacants']);
        $this->assertEquals(50.0, $stats['taux_occupation']); // 1/2 = 50%
    }
}
