<?php

namespace Tests\Feature;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Loyer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContratTest extends TestCase
{
    use RefreshDatabase;

    private const STATUT_OCCUPE = 'occupé';

    private $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    public function test_admin_can_create_contrat_and_it_updates_bien_status(): void
    {
        $this->withoutExceptionHandling();
        $bien = Bien::factory()->create(['statut' => 'libre']);
        $locataire = Locataire::factory()->create();

        $response = $this->actingAs($this->admin)
            ->postJson(route('contrats.store'), [
                'bien_id' => $bien->id,
                'locataire_id' => $locataire->id,
                'date_debut' => now()->format('Y-m-d'),
                'loyer_montant' => 200000,
                'caution' => 200000,
                'type_bail' => 'habitation',
                'statut' => 'actif',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('contrats', [
            'bien_id' => $bien->id,
            'locataire_id' => $locataire->id,
            'statut' => 'actif',
        ]);

        $this->assertEquals(self::STATUT_OCCUPE, $bien->fresh()->statut);
    }

    public function test_cannot_create_contrat_for_occupied_bien(): void
    {
        $bien = Bien::factory()->create(['statut' => self::STATUT_OCCUPE]);
        $locataire = Locataire::factory()->create();

        Contrat::factory()->create([
            'bien_id' => $bien->id,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
            ->postJson(route('contrats.store'), [
                'bien_id' => $bien->id,
                'locataire_id' => $locataire->id,
                'date_debut' => now()->addMonth()->format('Y-m-d'),
                'loyer_montant' => 200000,
                'type_bail' => 'habitation',
                'statut' => 'actif',
            ]);

        $response->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_updating_rent_propagates_to_unpaid_loyers(): void
    {
        $contrat = Contrat::factory()->create(['loyer_montant' => 100000]);

        $loyerEmis = Loyer::factory()->create([
            'contrat_id' => $contrat->id,
            'montant' => 100000,
            'statut' => 'émis',
        ]);

        $loyerPaye = Loyer::factory()->create([
            'contrat_id' => $contrat->id,
            'montant' => 100000,
            'statut' => 'payé',
        ]);

        $response = $this->actingAs($this->admin)
            ->putJson(route('contrats.update', $contrat), [
                'bien_id' => $contrat->bien_id,
                'locataire_id' => $contrat->locataire_id,
                'loyer_montant' => 120000,
                'date_debut' => $contrat->date_debut->format('Y-m-d'),
                'type_bail' => 'habitation',
                'statut' => $contrat->statut,
            ]);

        $response->assertStatus(200);

        $this->assertEquals(120000, $loyerEmis->fresh()->montant);
        $this->assertEquals(100000, $loyerPaye->fresh()->montant);
    }

    public function test_deleting_contrat_frees_up_bien(): void
    {
        $bien = Bien::factory()->create(['statut' => self::STATUT_OCCUPE]);
        $contrat = Contrat::factory()->create([
            'bien_id' => $bien->id,
            'statut' => 'actif',
        ]);

        $response = $this->actingAs($this->admin)
            ->deleteJson(route('contrats.destroy', $contrat));

        $response->assertStatus(200);
        $this->assertEquals('libre', $bien->fresh()->statut);
    }
}
